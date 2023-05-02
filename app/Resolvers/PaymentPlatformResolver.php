<?php 
namespace App\Resolvees;
use App\PymentPlatform;
class PaymentPlatformResolver
{
    protected $paymentPlatforms;

    public function __construct(){
        $this->paymentPlatforms =   PaymentPlatform::all();
    }

    public function resolveServices($paymentPlatformId){
        $name   =   strtolower(($this->paymentPlatforms->firstwhere('id',$paymentPlatformId)));

        $service    =   config("services.{$name}.class");

        if($service){
            return resolve($service);
            
        }
        
        throw new Exception('The selected payment platform is not in the configuration');

    }
}

?>