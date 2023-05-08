<?php 
namespace App\Resolvers;
use App\Models\PaymentPlatform;
use Exception;
class PaymentPlatformResolver
{
    protected $paymentPlatforms;

    public function __construct(){
        $this->paymentPlatforms =   PaymentPlatform::all();
    }

    public function resolveServices($paymentPlatformId){
        $paymentPlatform = $this->paymentPlatforms->firstwhere('id', $paymentPlatformId);
        $name = strtolower($paymentPlatform->name);

        $service    =   config("services.{$name}.class");

        if($service){
            return resolve($service);
            
        }
        
        throw new Exception('The selected payment platform is not in the configuration');

    }
}

?>