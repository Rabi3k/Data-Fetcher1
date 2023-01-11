<?php
namespace Src\Classes;

class Branch
{
    /*
    -- restaurant_branches
    rb.`id` as 'branch_id',
    rb.`city`, 
    rb.`zip_code`, 
    rb.`address`, 
    rb.`country`, 
    rb.`cvr` as 'branch_cvr',*/
#region Private props
    public int $id=0;
    public int $restaurantId = 0;
    public string $city="";
    public string $zip_code="";
    public string $address="";
    public string $country="";
    public string $cvr="";
    public array $secrets;
    public int $reference_id = 0;
#endregion
#region Construct
    public function __construct()
    {
        $this->secrets=array();
    }
    #endregion

    #region private functions
    #endregion

    #region public functions
    public function LoadBranch(int $id,
        int $restaurantId,
        string $city,
        string $zip_code,
        string $address,
        string $country,
        string $cvr,
        array $secrets,
        int $reference_id)
    {
        
        $this->id = $id;
        $this->restaurantId = $restaurantId;
        $this->city = $city;
        $this->zip_code = $zip_code;
        $this->address = $address;
        $this->country = $country;
        $this->cvr = $cvr;
        $this->secrets=$secrets;
        $this->reference_id = $reference_id;
    }

    #endregion
    #region static function

    public static function GetBranch(int $id,
    int $restaurantId,
    string $city,
    string $zip_code,
    string $address,
    string $country,
    string $cvr,
    array $secrets,
    int $reference_id)
    {
        $branch = new Branch();
        $branch->id = $id;
        $branch->restaurantId = $restaurantId;
        $branch->city = $city;
        $branch->zip_code = $zip_code;
        $branch->address = $address;
        $branch->country = $country;
        $branch->cvr = $cvr;
        $branch->secrets=$secrets;
        $branch->reference_id=$reference_id;
        return $branch;
    }
#endregion
}