#!/usr/bin/env bash
# Instalado en producción (13.140.189.137) como /usr/local/sbin/savitar-wg-add-peer,
# propiedad de root, modo 700. www-data lo puede ejecutar como root SOLO a través
# de una regla de sudoers dedicada (ver deploy/scripts/savitar-wg-sudoers), nunca
# con acceso genérico a `wg`/`tee`/shell. Copia de referencia — el original vive
# en el servidor y no se despliega por CI/CD (es infraestructura, no código de la app).
#
# Uso: savitar-wg-add-peer <clave_publica_wireguard> <ip_asignada>/32
# Llamado por App\Services\WireguardProvisioningService::addPeerToServer().

set -euo pipefail

PUBKEY="$1"
ALLOWED_IP="$2"
WG_INTERFACE="wg0"
WG_CONF="/etc/wireguard/wg0.conf"

if [[ ! "$PUBKEY" =~ ^[A-Za-z0-9+/]{43}=$ ]]; then
  echo "clave pública WireGuard inválida" >&2
  exit 1
fi

if [[ ! "$ALLOWED_IP" =~ ^10\.100\.100\.([0-9]{1,3})/32$ ]]; then
  echo "IP de túnel inválida (se espera 10.100.100.X/32)" >&2
  exit 1
fi

/usr/bin/wg set "$WG_INTERFACE" peer "$PUBKEY" allowed-ips "$ALLOWED_IP" persistent-keepalive 25

{
  echo ""
  echo "[Peer]"
  echo "PublicKey = $PUBKEY"
  echo "AllowedIPs = $ALLOWED_IP"
  echo "PersistentKeepalive = 25"
} >> "$WG_CONF"
