<?php
App::uses('AppController', 'Controller');

/**
 * Invoices Controller
 *
 * @property Invoice $Invoice
 * @property PaginatorComponent $Paginator
 * @property AuthComponent $Auth
 * @property SessionComponent $Session
 */
class InvoicesController extends AppController
{
    /**
     * Components
     *
     * @var array
     */
    public $components = array('Paginator', 'Auth', 'Session');

    /**
     * beforefilter method
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->_checkLogin();
    }

    /**
     * Execute Bill Cron
     */
    function exec_cron()
    {
        $this->Invoice->billCron();
    }

    /**
     * index method
     *
     * @return void
     */
    public function index($all = null)
    {
        $conditions = array('NOT' => array('Invoice.status' => 'deleted'));
        if(isCompany()){
            $conditions['user_id'] = getCompanyId();
        }
        if(isDealer()){
            $conditions['user_id'] = getDealerId();
        }
        if ($all == "all") {
            $this->Session->write('InvoiceSearch', '');
        }

        if (empty($this->request->data['Invoice']) && $this->Session->read('InvoiceSearch')) {
            $this->request->data['Invoice'] = $this->Session->read('InvoiceSearch');
        }
        if (!empty($this->request->data['Invoice'])) {
            $this->request->data['Invoice'] = array_filter($this->request->data['Invoice']);
            $this->request->data['Invoice'] = array_map('trim', $this->request->data['Invoice']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['Invoice']['name'])) {
                    $conditions['Invoice.name LIKE '] = '%' . $this->request->data['Invoice']['name'] . '%';
                }
                if (isset($this->request->data['Invoice']['id'])) {
                    $conditions['Invoice.id'] = $this->request->data['Invoice']['id'];
                }
                if (isset($this->request->data['Invoice']['customer_type'])) {
                    $conditions['Invoice.customer_type'] = $this->request->data['Invoice']['customer_type'];
                }
                if (isset($this->request->data['Invoice']['status'])) {
                    $conditions['Invoice.status'] = $this->request->data['Invoice']['status'];
                }
                if (isset($this->request->data['Invoice']['billed_date'])) {
                    $conditions['DATE_FORMAT(invoice_date,"%Y-%m")'] = date("Y-m", strtotime($this->request->data['Invoice']['billed_date']));
                }
            }
            $this->Session->write('InvoiceSearch', $this->request->data['Invoice']);
        }
        $this->AutoPaginate->setPaginate(array(
            'order' => ' Invoice.id DESC',
            'conditions' => $conditions
        ));
        $this->loadModel('Invoice');
        $this->set('invoices', $this->paginate('Invoice'));
    }

    function download($invoiceId = null)
    {
        $invoiceId = decrypt($invoiceId);

        if ($this->Invoice->exists($invoiceId)) {
            $invoiceDetail = $this->Invoice->find('first', array(
                'contain' => false,
                'conditions' => array('Invoice.id' => $invoiceId)
            ));
            $fileBaseName = $invoiceDetail['Invoice']['invoice_id'] . '.pdf';
             
            if(getInvoicePath($invoiceDetail['Invoice']['invoice_id'])!=""){
                 $this->response->file(getInvoicePath($invoiceDetail['Invoice']['invoice_id'], true), array('download' => true, 'name' => $fileBaseName));
           
                 return $this->response;
                  exit;
            }
            else{
                  $this->Message->setWarning(__('File Does not Exist'), $this->referer());
            }
           
           
        }
        $this->Message->setWarning(__('Invalid Invoice'), $this->referer());
    }

    function change_status($sId = null, $status = null)
    {
        $sId = decrypt($sId);
        $responseArr = array('status' => 'fail', 'message' => __('Unable to change status of invoice'));
        if ($this->Invoice->exists($sId) && !empty($status)) {
            $saveData = array(
                'status' => $status,
                'id' => $sId
            );
            if ($status == 'paid') {
                $saveData['paid_date'] = date('Y-m-d');
            }
            $this->Invoice->save($saveData);
            if ($status == 'paid') {
                /**
                 * sent mail to user for billed pay
                 */
            }
            $responseArr = array(
                'status' => 'success',
                'message' => __('Invoice status has been changed to ' . $status)
            );
        }
        echo json_encode($responseArr);
        exit;
    }
}
