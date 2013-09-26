<?php defined('SYSPATH') or die('No direct script access.');

class CouponSite_Mapper extends Model_Mapper {

    public function init()
    {
        parent::init();

        $this->add_column('coupon_id', array('Type' => 'int unsigned', 'Key' => 'INDEX'));
        $this->add_column('site_id',   array('Type' => 'int unsigned', 'Key' => 'INDEX'));
    }

    /**
     * Link coupon to sites
     * 
     * @param Model_Payment $coupon
     * @param array $sites array(site_id => TRUE/FALSE)
     */
    public function link_coupon_to_sites(Model_Coupon $coupon, array $sites)
    {
        $this->delete_rows(DB::where('coupon_id', '=', (int) $coupon->id));
        foreach ($sites as $site_id => $selected)
        {
            if ($selected)
            {
                $this->insert(array(
                    'coupon_id' => $coupon->id,
                    'site_id'   => $site_id
                ));
            }
        }
    }
}