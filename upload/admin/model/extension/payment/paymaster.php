<?php

class ModelExtensionPaymentPaymaster extends Model
{

    public function install()
    {
        $query = "SELECT xml FROM " . DB_PREFIX . "modification WHERE code = 'PayMaster' LIMIT 1";
        $res = $this->db->query($query);

        if ($res->num_rows > 0) {
            $xml = $res->row['xml'];

            $dom = new DOMDocument('1.0', 'UTF-8');
            $dom->loadXml($xml);

            $defaultValues = array(
                'payment_paymaster_base_address' => $dom->getElementsByTagName('base_service_url')->item(0)->nodeValue,
                'payment_paymaster_service_name' => $dom->getElementsByTagName('display_service_name')->item(0)->nodeValue,
                'payment_paymaster_send_receipt_data' => $dom->getElementsByTagName('send_receipt_data')->item(0)->nodeValue
            );
            $this->load->model('setting/setting');
            $this->model_setting_setting->editSetting('payment_paymaster', $defaultValues);
        }
    }
}