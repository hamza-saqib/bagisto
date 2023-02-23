<?php

namespace App\Http\Controllers;

use App\AssetsModel;
use App\FabricsModel;
use App\StylesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FabricController extends Controller
{
    public function index(){
        $where = [];

        if(isset($_GET['product_id']))
        {
            $where = ['product_id' => $_GET['product_id']];
        }
        $fabrics = FabricsModel::where($where)->get();
        if(count($fabrics) == 0){
            return response()->json([
                'status' => false,
                'result' => [],
                'message' => "No Fabrics Found"
            ], 400);    
        }
        return response()->json([
            'status' => true,
            'result' => $fabrics
        ], 200);
    }

    public function store(Request $request){
        
    // $validator = Validator::make($request->all(), [
    //     "name" => "required",
    //     "color" => "required"
    // ]);
    // if ($validator->fails()) {
    //     return response()->json([
    //         'status' => false,
    //         'message' => $validator->errors()
    //     ], 400);
    // } // validate fails

        $fabrics = $request->all();
        $checkFabricExist = FabricsModel::where([
            'name' => $fabrics[0]['name'],
            'color' => $fabrics[0]['color']
        ])->exists();
        if($checkFabricExist)
        {
            return response()->json([
             'status' => false,
             'message' => 'This fabric is already exist'
         ], 400);
        }
        foreach($fabrics as $key => $fabric){
            $fabricData = [
                'name'          => isset($fabric['name']) ? $fabric['name'] : ' - ',
                'color'         => isset($fabric['color']) ? $fabric['color'] : ' - ',
                'image'         => isset($fabric['image']) ? $fabric['image'] : ' - ',
                'image_link'    => isset($fabric['image_link']) ? $fabric['image_link'] : ' - ',
                'product_id'    => isset($fabric['product_id']) ? $fabric['product_id'] : ' - ',
            ];

            $fabricInserted =  FabricsModel::create($fabricData);
            $fabricId = $fabricInserted->id;
            $productId = $fabric['product_id'];
            foreach($fabric['assets'] as $assetKey => $asset){
                $checkAssetExists = AssetsModel::where('name', $assetKey)->first();
                if(isset($checkAssetExists->id)){
                    $assetId = $checkAssetExists->id;
                }
                else
                {
                    $assetData = [
                        'name' => $assetKey,
                        'product_id' => $productId
                    ];
    
                    $assetInserted = AssetsModel::create($assetData);
                    $assetId = $assetInserted->id;
                }
                foreach($asset as $assetTypeKey => $assetType){
                    $checkAssetTypeExists = AssetsModel::where(['name' => $assetTypeKey, 'parent_id' => $assetId])->first();
                    if(isset($checkAssetTypeExists->id)){
                        $assetTypeId = $checkAssetTypeExists->id;
                    }
                    else
                    {
                        $assetData = [
                            'name' => $assetTypeKey,
                            'image' => isset($assetType['image']) ? $assetType['image'] : ' - ',
                            'image_link' => isset($assetType['image_link']) ? $assetType['image_link'] : ' - ',
                            'product_id' => $productId,
                            'parent_id' => $assetId
                        ];
        
                        $assetTypeInserted = AssetsModel::create($assetData);
                        $assetTypeId = $assetTypeInserted->id;
                    }
                    foreach($assetType['styles'] as $styleKey => $style){
                        $styleData = [
                            'name' => isset($style['name']) ? $style['name'] : ' - ',
                            'image' => isset($style['image']) ? $style['image'] : ' - ',
                            'image_link' => isset($style['image_link']) ? $style['image_link'] : ' - ',
                            'product_id' => $productId,
                            'asset_id' => $assetTypeId,
                            'fabric_id' => $fabricId
                        ];  
    
                        $styleInserted = StylesModel::create($styleData);
                        $styleId = $styleInserted->id;
                    }
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Fabric has been added successfully'
        ], 200);
    }

    function getFabricPicturesById(Request $request)
    {
        $fabricId = $request->input('fabricId');
        $productId = $request->input('productId');
        $fabricProductArray = [];
        $fabricArray = [];
        $fabricPicturesData = FabricsModel::where(['id' => $fabricId, 'product_id' => $productId])->get();
        if(count($fabricPicturesData) == 0){
            return response()->json([
                'status' => false,
                'result' => [],
                'message' => "No Fabric Asset Images Found"
            ], 400);    
        }
        $fabricAssets = AssetsModel::where('assets.product_id', $productId)
        ->select('assets.name as parent_name' ,'a.*')
        ->join('assets as a', 'a.parent_id', "=", "assets.id")
        ->get();
        
        foreach($fabricPicturesData as $results){
            $fabricProductArray = [
                'name' => $results['name'],
                'color' => $results['color'],
                'product_id' => $results['product_id'],
                'image' => $results['image'],
                "image_link" => $results['image_link']
            ]; 
        }

        foreach($fabricAssets as $assetKey => $asset){
            
            $styles = StylesModel::where(['fabric_id' => $fabricId, 'asset_id' => $asset['id']])->get();
            $styleArray = [];
            foreach($styles as $style){
                $styleArray[] = [
                    'name' => $style['name'],
                    'image' => $style['image'],
                    'image_link' => $style['image_link']
                ];
            }

            $fabricArray[$asset['name']][$asset['parent_name']][] = [
                "image" => $asset['image'],
                "image_link" => $asset['image_link'],
                'styles' => $styleArray
            ];
        }   
        
        $fabricProductArray['assets'] = $fabricArray;
        return response()->json([
            'status' => true,
            'result' => $fabricProductArray
        ], 200);
    }
    private function generateRandomString($length = 10) {
       $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    function generatePresignedUrl(Request $request){
        
        $imageCount = $request->header('imageCount');
        
        $urls = [];
        for($i=1; $i<=$imageCount; $i++)
        {
            $imgName = $this->generateRandomString();
            $s3 = App::make('aws')->createClient('s3');   
            $cmd = $s3->getCommand('PutObject', [
                'Bucket' => env("AWS_BUCKET"),
                'Key' =>  $imgName,
                'ResponseContentDisposition' => 'attachment;filename='. $imgName,
                'ResponseContentLanguage' => 'en'
            ]);     
            $request = $s3->createPresignedRequest($cmd, '+120 minutes');

            $presignedUrl = (string)$request->getUri();
            $urls[$imgName] = $presignedUrl;
        }

        return response()->json([
            'status' => true,
            'result' => $urls
        ], 200);
        
    }
}

