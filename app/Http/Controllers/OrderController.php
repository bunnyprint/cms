<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderQuantity;
use App\Models\ProductMaterial;
use App\Models\ProductShape;
use App\Models\ProductDelivery;
use App\Models\ProductLamination;
use App\Models\QuantityMultiplier;
use App\Services\QuantityMultiplierService;
use App\Traits\HasProductBinding;

class OrderController extends Controller
{
    use HasProductBinding;

    private $quantityMultiplierService;
    // constructor
    public function __construct(QuantityMultiplierService $quantityMultiplierService)
    {
        $this->quantityMultiplierService = $quantityMultiplierService;
        $this->middleware('auth', ['except' => [
            'getLabelstickerQuotationApi'
        ]]);
    }

    // return order quotation index
    public function index($type = 'customer')
    {
        return view('order.index', compact('type'));
    }

    // return label sticker api()
    public function getLabelstickerQuotationApi(Request $request)
    {
        $product_id = 1;

        $this->validate(request(), [
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'material_id' => 'required',
            'orderquantity_id' => 'required',
            'shape_id' => 'required',
        ], [
            'material_id.required' => 'Please select a material',
            'orderquantity_id.required' => 'Please select the quantities',
            'shape_id.required' => 'Please select the shape'
        ]);

        $width = request('width');
        $height = request('height');
        $material_id = request('material_id')['id'];
        $orderquantity_id = request('orderquantity_id')['id'];
        $lamination_id = request('lamination_id')['id'];
        $shape_id = request('shape_id')['id'];
        $delivery_id = request('delivery_id')['id'];
        $type = request('type') ? request('type') : 'customer';
        $dimension = [
            'width' => 0,
            'height' => 0
        ];
        $floor_width = 0;
        $floor_height = 0;
        $area = 0;
        $formula = 0;
        $total = 0;
        // dd($material_id, $shape_id, $lamination_id, $orderquantity_id, $delivery_id);
        // dd($orderquantity_id);

        $dimension = [
            'width' => 317,
            'height' => 470
        ];
        $this->validate(request(), [
            'width' => 'lte:317',
            'height' => 'lte:470'
            // 305
            // 455
        ]);

// 310/(50+3)== 5
// (qty 1000)/5 = 20 * 53 == 10600mm == 10.6m
/*
        $width = floor($dimension['width'] / ($width + 3)); [get the floor value, etc 2.38 ---> 2]
        $height = floor($dimension['height']/ ($height + 3));

        $area = $width x $height;

        $formula = round($qty/ $area) + 1; [rounding quantity divide area]

        $total = ($formula * $quantitymultiplier * $material->multiplier * $shape->multiplier * $lamination->multiplier : 1) + $delivery->multiplier;
 */

        $floor_width1 = floor($dimension['width'] / ($width + 3));
        $floor_height1 = floor($dimension['height']/ ($height + 3));

        $floor_width2 = floor($dimension['width']/ ($height + 3));
        $floor_height2 = floor($dimension['height']/ ($width + 3));

        $area1 = $floor_width1 * $floor_height1;
        $area2 = $floor_width2 * $floor_height2;

        if($area1 >= $area2){
            $area = $area1;
        }else {
            $area = $area2;
        }

        $orderquantity = OrderQuantity::findOrFail($orderquantity_id);
        $material = ProductMaterial::find($material_id);
        $shape = ProductShape::find($shape_id);
        $delivery = ProductDelivery::find($delivery_id);
        $lamination = ProductLamination::find($lamination_id);

        $formula = intval(round($orderquantity->qty/ $area) + 1);

        $input['product_id'] = $product_id;
        $input['min'] = $formula;
        $input['max'] = $formula;
        $input['type'] = $type;
        $quantitymultiplier = $this->quantityMultiplierService->getOneByFilter($input);
            // ->get();
            // dd($formula, $product_id, $quantitymultiplier->toArray());

        // dd(
        //     $material->customerMultipliers->first()->value,
        //     $shape->customerMultipliers->first()->value,
        //     $lamination->customerMultipliers->first()->value,
        //     $delivery->customerMultipliers->first()->value,
        //     $quantitymultiplier->customerMultipliers->first()->value
        // );


        $materialMultiplier = 0;
        $shapeMultiplier = 0;
        $laminationMultiplier = 0;
        $deliveryMultiplier = 0;
        // $quantityMultiplier = 0;

        switch($type) {
            case 'customer':
                $materialMultiplier = $material ? $material->customerMultipliers->first()->value : 1;
                $shapeMultiplier = $shape ? $shape->customerMultipliers->first()->value : 1;
                $laminationMultiplier = $lamination ? $lamination->customerMultipliers->first()->value : 1;
                $deliveryMultiplier = $delivery ? $delivery->customerMultipliers->first()->value : 1;
                $quantityMultiplier = $quantitymultiplier ? $quantitymultiplier->customerMultipliers->first()->value : 1;
                break;
            case 'agent':
                $materialMultiplier = $material ? $material->agentMultipliers->first()->value : 1;
                $shapeMultiplier = $shape ? $shape->agentMultipliers->first()->value : 1;
                $laminationMultiplier = $lamination ? $lamination->agentMultipliers->first()->value : 1;
                $deliveryMultiplier = $delivery ? $delivery->agentMultipliers->first()->value : 1;
                $quantityMultiplier = $quantitymultiplier ? $quantitymultiplier->agentMultipliers->first()->value : 1;
                break;
        }
        // dd($formula * (float)$materialMultiplier * (float)$shapeMultiplier * (float)$laminationMultiplier + (float)$deliveryMultiplier, $quantityMultiplier);

        $total = $formula * (float)$quantityMultiplier * (float)$materialMultiplier * (float)$shapeMultiplier * (float)$laminationMultiplier + (float)$deliveryMultiplier;
        // dd($total);

        return ceil($total);
    }

    // return inkjet sticker index
    public function getInkjetstickerIndex()
    {
        return view('client.inkjetsticker');
    }

    // return inkjet sticker api()
    public function getInkjetstickerQuotationApi()
    {
        $product_id = 2;

        $this->validate(request(), [
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'material_id' => 'required',
            // 'orderquantity_id' => 'required',
            'shape_id' => 'required',
        ], [
            'material_id.required' => 'Please select a material',
            // 'orderquantity_id.required' => 'Please select the quantities',
            'shape_id.required' => 'Please select the shape'
        ]);

        $width = request('width');
        $height = request('height');
        $material_id = request('material_id');
        $orderquantity_id = request('orderquantity_id');
        $quantities = request('quantities');
        $shape_id = request('shape_id');
        $lamination_id = request('lamination_id');
        $finishing_id = request('finishing_id');
        $frame_id = request('frame_id');
        $delivery_id = request('delivery_id');
        $dimension = [
            'width' => 0,
            'height' => 0
        ];
        $cal_width = 0;
        $cal_height = 0;
        $area = 0;
        $formula = 0;
        $total = 0;

        $dimension = [
            'width' => 150,
            'height' => 500
        ];
/*
        $this->validate(request(), [
            'width' => 'lte:150',
            'height' => 'lte:500'
        ]); */

        $cal_width = $width * 0.035;
        $cal_height = $height * 0.035;

        $area = round($cal_width * $cal_height, 2);
/*
        $material = Productmaterial::where('material_id', $material_id->id)->where('product_id', $product_id)->first();
        $shape = Productshape::where('shape_id', $shape_id->id)->where('product_id', $product_id)->first();
        $lamination = Productlamination::where('lamination_id', $lamination_id->id)->where('product_id', $product_id)->first();
        $finishing = Productfinishing::where('finishing_id', $finishing_id->id)->where('product_id', $product_id)->first();
        $delivery = Productdelivery::where('delivery_id', $delivery_id->id)->where('product_id', $product_id)->first(); */
        // dd(request()->all());

        // $total = $shape->multiplier : 0 * ($material ? $material->multiplier : 0 + $lamination ? $lamination->multiplier : 0 + $finishing ? $finishing->multiplier : 0) * $area;

        $total = $shape->multiplier * ($material->multiplier + $lamination->multiplier + $finishing->multiplier);

        // $total = $total * $orderquantity->qty;
        $total = $total * $quantities;

        // dd($delivery);
        $total = $total + ($delivery ? $delivery->multiplier : 0);

        // dd($total);

        return $total;

    }
}
