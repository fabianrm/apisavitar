#!/usr/bin/env bash
# Instalado en producción (13.140.189.137) como /usr/local/sbin/savitar-wg-remove-peer,
# propiedad de root, modo 700. Contraparte de savitar-wg-add-peer.sh — ver ese
# archivo para el contexto de por qué esto vive fuera del control de CI/CD.
#
# Uso: savitar-wg-remove-peer <clave_publica_wireguard>
# Llamado por App\Services\WireguardProvisioningService::removePeerFromServer().

set -euo pipefail

PUBKEY="$1"
WG_INTERFACE="wg0"
WG_CONF="/etc/wireguard/wg0.conf"

if [[ ! "$PUBKEY" =~ ^[A-Za-z0-9+/]{43}=$ ]]; then
  echo "clave pública WireGuard inválida" >&2
  exit 1
fi

/usr/bin/wg set "$WG_INTERFACE" peer "$PUBKEY" remove

# Quita el bloque [Peer] correspondiente de wg0.conf (la línea [Peer], su
# PublicKey, y las líneas siguientes hasta el próximo bloque o el fin del
# archivo). Si por algo el bloque no existe en el archivo (solo estaba en
# memoria), no falla: simplemente no hay nada que borrar.
awk -v pubkey="$PUBKEY" '
  /^\[Peer\]/ {
    if (in_block && !is_target) printf "%s", block
    block = $0 "\n"
    in_block = 1
    is_target = 0
    next
  }
  in_block && /^\[/ {
    if (!is_target) printf "%s", block
    in_block = 0
  }
  in_block {
    block = block $0 "\n"
    if ($0 == "PublicKey = " pubkey) is_target = 1
    next
  }
  { print }
  END {
    if (in_block && !is_target) printf "%s", block
  }
' "$WG_CONF" > "${WG_CONF}.tmp" && mv "${WG_CONF}.tmp" "$WG_CONF"
