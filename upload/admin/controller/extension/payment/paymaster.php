<?php
class ControllerExtensionPaymentPaymaster extends Controller
{   
    public function install()
    {
        $this->load->model('extension/payment/paymaster');
        $this->model_extension_payment_paymaster->install();
    }

    public function index()
    {
        $this->load->language('extension/payment/paymaster');
        
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $validation_error = $this->validate();
            if (empty($validation_error)) {
                $this->load->model('setting/setting');
                $this->model_setting_setting->editSetting('payment_paymaster', $this->request->post);
                $this->session->data['success'] = $this->language->get('text_success');
                $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
            }
            else {
                $data['validation_error'] = $validation_error;
                
                $data['payment_paymaster_status'] = $this->request->post['payment_paymaster_status'];
                $data['payment_paymaster_base_address'] = $this->request->post['payment_paymaster_base_address'];
                $data['payment_paymaster_token'] = $this->request->post['payment_paymaster_token'];
                $data['payment_paymaster_merchant_id'] = $this->request->post['payment_paymaster_merchant_id'];
                $data['payment_paymaster_service_name'] = $this->request->post['payment_paymaster_service_name'];
                $data['payment_paymaster_done_status_id'] = $this->request->post['payment_paymaster_done_status_id'];
                $data['payment_paymaster_geo_zone_id'] = $this->request->post['payment_paymaster_geo_zone_id'];
                $data['payment_paymaster_log'] = $this->request->post['payment_paymaster_log'];
                $data['payment_paymaster_sort_order'] = $this->request->post['payment_paymaster_sort_order'];
                
                if (isset($this->request->post['payment_paymaster_send_receipt_data'])) {
                    $data['payment_paymaster_send_receipt_data'] = $this->request->post['payment_paymaster_send_receipt_data'];
                    $data['payment_paymaster_tax_rules'] = $this->request->post['payment_paymaster_tax_rules'];
                    $data['payment_paymaster_default_vat_type'] = $this->request->post['payment_paymaster_default_vat_type'];
                    $data['payment_paymaster_payment_subject'] = $this->request->post['payment_paymaster_payment_subject'];
                    $data['payment_paymaster_payment_subject_for_shipping'] = $this->request->post['payment_paymaster_payment_subject_for_shipping'];
                    $data['payment_paymaster_payment_method'] = $this->request->post['payment_paymaster_payment_method'];
                    $data['payment_paymaster_payment_method_for_shipping'] = $this->request->post['payment_paymaster_payment_method_for_shipping'];
                }
            }
        }
        else {
            $data['payment_paymaster_status'] = $this->config->get('payment_paymaster_status');
            $data['payment_paymaster_base_address'] = $this->config->get('payment_paymaster_base_address');
            $data['payment_paymaster_token'] = $this->config->get('payment_paymaster_token');
            $data['payment_paymaster_merchant_id'] = $this->config->get('payment_paymaster_merchant_id');
            $data['payment_paymaster_service_name'] = $this->config->get('payment_paymaster_service_name');       
            $data['payment_paymaster_done_status_id'] = $this->config->get('payment_paymaster_done_status_id');
            $data['payment_paymaster_geo_zone_id'] = $this->config->get('payment_paymaster_geo_zone_id');
            $data['payment_paymaster_log'] = $this->config->get('payment_paymaster_log');
            $data['payment_paymaster_sort_order'] = $this->config->get('payment_paymaster_sort_order');
            
            $data['payment_paymaster_send_receipt_data'] = $this->config->get('payment_paymaster_send_receipt_data');
            $data['payment_paymaster_tax_rules'] = $this->config->get('payment_paymaster_tax_rules') ?? array();
            $data['payment_paymaster_default_vat_type'] = $this->config->get('payment_paymaster_default_vat_type');
            $data['payment_paymaster_payment_subject'] = $this->config->get('payment_paymaster_payment_subject');
            $data['payment_paymaster_payment_subject_for_shipping'] = $this->config->get('payment_paymaster_payment_subject_for_shipping');
            $data['payment_paymaster_payment_method'] = $this->config->get('payment_paymaster_payment_method');
            $data['payment_paymaster_payment_method_for_shipping'] = $this->config->get('payment_paymaster_payment_method_for_shipping');
        }
        
        $this->document->setTitle($this->language->get('heading_title'));
        
        $data['back_link'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment');
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_extension'),
            'href' => $data['back_link']
        ];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/paymaster', 'user_token=' . $this->session->data['user_token'])
        ];
        
        $data['form_action'] = $this->url->link('extension/payment/paymaster', 'user_token=' . $this->session->data['user_token']);
        
        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('localisation/tax_class');
        $data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
        
        $data['tax_rules'] = $this->get_vat_types();
        $data['types_of_goods'] = $this->get_types_of_goods();
        $data['payment_methods'] = $this->get_payment_methods();

        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('extension/payment/paymaster', $data));
    }
    
    private function get_vat_types(): array
    {
        return array(
            array(
                'name' => $this->language->get('vat_none'),
                'value' => 'None'
            ),
            array(
                'name' => $this->language->get('vat_0'),
                'value' => 'Vat0'
            ),
            array(
                'name' => $this->language->get('vat_10'),
                'value' => 'Vat10'
            ),
            array(
                'name' => $this->language->get('vat_20'),
                'value' => 'Vat20'
            ),
            array(
                'name' => $this->language->get('vat_110'),
                'value' => 'Vat110'
            ),
            array(
                'name' => $this->language->get('vat_120'),
                'value' => 'Vat120'
            )
        );
    }
    
    private function get_types_of_goods(): array
    {
        return array(
            array(
                'name' => $this->language->get('payment_subject_commodity'),
                'value' => 'Commodity'
            ),
            array(
                'name' => $this->language->get('payment_subject_excise'),
                'value' => 'Excise'
            ),
            array(
                'name' => $this->language->get('payment_subject_job'),
                'value' => 'Job'
            ),
            array(
                'name' => $this->language->get('payment_subject_service'),
                'value' => 'Service'
            ),
            array(
                'name' => $this->language->get('payment_subject_gambling'),
                'value' => 'Gambling'
            ),
            array(
                'name' => $this->language->get('payment_subject_lottery'),
                'value' => 'Lottery'
            ),
            array(
                'name' => $this->language->get('payment_subject_intellectual_activity'),
                'value' => 'IntellectualActivity'
            ),
            array(
                'name' => $this->language->get('payment_subject_payment'),
                'value' => 'Payment'
            ),
            array(
                'name' => $this->language->get('payment_subject_agent_fee'),
                'value' => 'AgentFee'
            ),
            array(
                'name' => $this->language->get('payment_subject_property_rights'),
                'value' => 'PropertyRights'
            ),
            array(
                'name' => $this->language->get('payment_subject_non_operating_income'),
                'value' => 'NonOperatingIncome'
            ),
            array(
                'name' => $this->language->get('payment_subject_insurance_payment'),
                'value' => 'InsurancePayment'
            ),
            array(
                'name' => $this->language->get('payment_subject_sales_tax'),
                'value' => 'SalesTax'
            ),
            array(
                'name' => $this->language->get('payment_subject_resort_fee'),
                'value' => 'ResortFee'
            ),
            array(
                'name' => $this->language->get('payment_subject_other'),
                'value' => 'Other'
            )
        );
    }
    
    private function get_payment_methods(): array
    {
        return array(
            array(
                'name' => $this->language->get('payment_method_full_prepayment'),
                'value' => 'FullPrepayment'
            ),
            array(
                'name' => $this->language->get('payment_method_partial_prepayment'),
                'value' => 'PartialPrepayment'
            ),
            array(
                'name' => $this->language->get('payment_method_advance'),
                'value' => 'Advance'
            ),
            array(
                'name' => $this->language->get('payment_method_full_payment'),
                'value' => 'FullPayment'
            ),
            array(
                'name' => $this->language->get('payment_method_partial_payment'),
                'value' => 'PartialPayment'
            ),
            array(
                'name' => $this->language->get('payment_method_credit'),
                'value' => 'Credit'
            )
        );
    }

    private function validate(): string
    {
        $error = '';
        
        if (!$this->user->hasPermission('modify', 'extension/payment/paymaster')) {
            $error = $this->language->get('error_permission');
        }
        if (empty($error)) {
            $base_address = $this->request->post['payment_paymaster_base_address'];
            if (is_null($base_address) || strpos(strtolower($base_address), 'https://') !== 0 || !filter_var($base_address, FILTER_VALIDATE_URL))
                $error = $this->language->get('error_base_address');
        }
        if (empty($error) && !$this->request->post['payment_paymaster_token']) {
            $error = $this->language->get('error_token');
        }
        if (empty($error) && !$this->request->post['payment_paymaster_merchant_id']) {
            $error = $this->language->get('error_entry_merchant_id');
        }
        if (empty($error) && !$this->request->post['payment_paymaster_service_name']) {
            $error = $this->language->get('error_entry_service_name');
        }

        return $error;
    }
}

