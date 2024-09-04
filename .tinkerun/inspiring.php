<?php

use App\Models\OutputDetail;
use Illuminate\Support\Facades\DB;

$destinationId = 2; // Aquí pones el destination_id que deseas filtrar

OutputDetail::select('outputs.date', 'materials.code', 'materials.name', 'brands.name as brand', 'materials.model', 'presentations.name as presentation', 'output_details.quantity', 'output_details.subtotal')
    ->join('entry_details', 'output_details.entry_detail_id', '=', 'entry_details.id')
    ->join('materials', 'entry_details.material_id', '=', 'materials.id')
    ->join('presentations', 'presentations.id', '=', 'materials.presentation_id')
    ->join('brands', 'brands.id', '=', 'materials.brand_id')
    ->join('outputs', 'output_details.output_id', '=', 'outputs.id')
    ->where('outputs.destination_id', $destinationId)
    ->get();


