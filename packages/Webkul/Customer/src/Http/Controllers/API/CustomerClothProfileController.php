<?php

namespace Webkul\Customer\Http\Controllers\API;

use Webkul\Customer\Repositories\CustomerAddressRepository;
use Webkul\API\Http\Resources\Customer\CustomerAddress as CustomerAddressResource;
use Webkul\Customer\Models\CustomerClothProfile;
use App\Http\Controllers\Controller;

class CustomerClothProfileController extends Controller
{
    /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * CustomerAddressRepository object
     *
     * @var \Webkul\Customer\Repositories\CustomerAddressRepository
     */
    protected $customerAddressRepository;

    /**
     * Controller instance
     *
     * @param CustomerAddressRepository $customerAddressRepository
     */
    public function __construct(CustomerAddressRepository $customerAddressRepository)
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('auth:' . $this->guard);

        $this->_config = request('_config');

        // $this->customerAddressRepository = $customerAddressRepository;
    }

    /**
     * Get user address.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $customer = auth($this->guard)->user();

        return CustomerClothProfile::where('customer_id', $customer->id)->get()->toArray();
    }

    /**
     * Get user address.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function get()
    {
        $customer = auth($this->guard)->user();

        $addresses = $customer->addresses()->get();

        return CustomerAddressResource::collection($addresses);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store()
    {
        $customer = auth($this->guard)->user();

        if (request()->input('profile_data') && ! is_array(request()->input('profile_data'))) {
            return response()->json([
                'message' => 'profile_data must be an array.',
            ]);
        }

        $this->validate(request(), [
            'name' => 'string|required',
            'profile_data' => 'array|required',
        ]);

        $customerClothProfile = CustomerClothProfile::create([
            'name' => request()->input('name'),
            'customer_id' => $customer->id,
            'profile_data' => request()->input('profile_data')
        ]);

        return response()->json([
            'message' => 'Your Cloth Profile has been created successfully.',
            'data'    => $customerClothProfile,
        ]);
    }

    /**
     * Update the specified resource in storage.
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(int $id)
    {
        $customer = auth($this->guard)->user();

        if (request()->input('profile_data') && ! is_array(request()->input('profile_data'))) {
            return response()->json([
                'message' => 'profile_data must be an array.',
            ]);
        }

        $this->validate(request(), [
            'name' => 'string|required',
            'profile_data' => 'array|required',
        ]);

        $customerClothProfile = CustomerClothProfile::find($id);

        if($customerClothProfile){
            if($customerClothProfile == $customer->id){
                $customerClothProfile->name = request()->input('name');
                $customerClothProfile->profile_data = request()->input('profile_data');
                $customerClothProfile->save();
                return response()->json([
                    'message' => 'Your Cloth Profile has been updated successfully.',
                    'data'    => $customerClothProfile,
                ]);
            } else {
                return response()->json([
                    'message' => 'you are not authrized to update this profile, you can only update your cloth profiles.',
                ]);
            }
        } else {
            return response()->json([
                'message' => 'cloth profile not found with id ' . $id . '.',
            ]);
        }

    }
}
