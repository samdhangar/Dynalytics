<?php
App::uses('AppController', 'Controller');

class SubscriptionsController extends AppController
{
    public $components = array('Paginator', 'Auth', 'Session');

    //put your code here
    function beforeFilter()
    {
        $this->_checkLogin();
        parent::beforeFilter();
    }

    /**
     * index method
     *
     * @return void
     */
    public function index($all = null)
    {
        $conditions = array('NOT' => array('Subscription.status' => 'deleted'));
        if ($all == "all") {
            $this->Session->write('SubscriptionSearch', '');
        }

        if (empty($this->request->data['Subscription']) && $this->Session->read('SubscriptionSearch')) {
            $this->request->data['Subscription'] = $this->Session->read('SubscriptionSearch');
        }
        if (!empty($this->request->data['Subscription'])) {
            $this->request->data['Subscription'] = array_filter($this->request->data['Subscription']);
            $this->request->data['Subscription'] = array_map('trim', $this->request->data['Subscription']);
            if (!empty($this->request->data)) {
                if (isset($this->request->data['Subscription']['name'])) {
                    $conditions['Subscription.name LIKE '] = '%' . $this->request->data['Subscription']['name'] . '%';
                }
                if (isset($this->request->data['Subscription']['type'])) {
                    $conditions['Subscription.type'] = $this->request->data['Subscription']['type'];
                }
                if (isset($this->request->data['Subscription']['charge'])) {
                    $conditions['Subscription.charge'] = $this->request->data['Subscription']['charge'];
                }
                if (isset($this->request->data['Subscription']['status'])) {
                    $conditions['Subscription.status'] = $this->request->data['Subscription']['status'];
                }
            }
            $this->Session->write('SubscriptionSearch', $this->request->data['Subscription']);
        }
        $this->AutoPaginate->setPaginate(array(
            'contain' => false,
            'order' => ' Subscription.id DESC',
            'conditions' => $conditions
        ));
        $this->loadModel('Subscription');
        $this->set('subscriptions', $this->paginate('Subscription'));
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function view($id = null)
    {
        $id = decrypt($id);
        if (!$this->Subscription->exists($id)) {
            $this->Message->setWarning(__('Invalid subscription'), array('action' => 'index'));
        }
        $options = array('contain' => false, 'conditions' => array('Subscription.' . $this->Subscription->primaryKey => $id));
        $this->set('subscription', $this->Subscription->find('first', $options));
    }

    /**
     * add method
     *
     * @return void
     */
    public function add()
    {
        $sessionData = getMySessionData();
        if ($this->request->is('post')) {
            $this->Subscription->create();
            if ($this->Subscription->save($this->request->data)) {
                $this->Message->setSuccess(__('The subscription has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The subscription could not be saved. Please, try again.'));
            }
        }
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function edit($id = null)
    {
        $sessionData = getMySessionData();
        $id = decrypt($id);
        if (!$this->Subscription->exists($id)) {
            $this->Message->setWarning(__('Invalid subscription'), array('action' => 'index'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->Subscription->save($this->request->data)) {
                $this->Message->setSuccess(__('The subscription has been updated.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Message->setWarning(__('The subscription could not be updated. Please, try again.'));
            }
        } else {
            $options = array('contain' => false, 'conditions' => array('Subscription.' . $this->Subscription->primaryKey => $id));
            $this->request->data = $this->Subscription->find('first', $options);
        }
        $this->set('edit', 1);
        $this->render('add');
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function delete($id = null)
    {
        if (!empty($this->request->data['delete']['id']) && is_array($this->request->data['delete']['id'])) {
            foreach ($this->request->data['delete']['id'] as $key => $del_id) {
                $this->request->data['delete']['id'][$key] = decrypt($del_id);
            }
        }
        if ($this->request->is(array('post', 'put')) && !empty($this->request->data['delete']['id'])) {
            $this->Subscription->updateAll(array('status' => "'deleted'"), array('Subscription.id' => array_values($this->request->data['delete']['id'])));
            $this->Message->setSuccess(__('The Subscription has been deleted.'), $this->referer());
        }
        if (empty($id)) {
            $this->Message->setWarning(__('Invalid Operation.'), $this->referer());
        }
        $id = decrypt($id);
        $this->Subscription->id = $id;
        if (!$this->Subscription->exists()) {
            $this->Message->setWarning(__('Invalid subscription'), array('action' => 'index'));
        }
        if ($this->Subscription->saveField('status', 'deleted')) {
            $this->Message->setSuccess(__('The subscription has been deleted.'));
        } else {
            $this->Message->setWarning(__('The subscription could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('action' => 'index'));
    }

    function change_status($sId = null, $status = null)
    {
        $sId = decrypt($sId);
        $responseArr = array('status' => 'fail', 'message' => __('Unable to change status of subscription'));
        if ($this->Subscription->exists($sId) && !empty($status)) {
            $this->Subscription->id = $sId;
            $this->Subscription->saveField('status', $status);
            $responseArr = array(
                'status' => 'success',
                'message' => __('Subscription status has been changed to ' . $status)
            );
        }
        echo json_encode($responseArr);
        exit;
    }

    function generate_pdf($invoiceId = null, $companyId = null)
    {
        $this->loadModel('Invoice');
        $this->loadModel('User');
        $this->loadModel('FileProccessingDetail');
        $invoiceDetail = $this->Invoice->find('first', array(
            'contain' => false,
            'conditions' => array('Invoice.id' => $invoiceId)
        ));
        $totalStationFile = $this->FileProccessingDetail->getStationFileInLastMonth(array('FileProccessingDetail.company_id' => $companyId));
        $companyDetail = $this->User->find('first', array(
            'fields' => array(
                'id', 'first_name', 'last_name',
                'email', 'phone_no', 'created',
                'name', 'subscription_id',
                'company_branch_count', 'station_count',
                'role'
            ),
            'contain' => array(
                'Subscription' => array(
                    'id', 'setup_cost', 'branch_cost', 'charge', 'type'
                )
            ),
            'conditions' => array(
                'User.id' => $companyId
            )
        ));
        $companyDetail['User']['station_file'] = $totalStationFile;
        $countInvoice = $this->Invoice->find('count', array('conditions' => array('user_id' => $companyId)));
        $billingData = array(
            array(
                'particlars' => __('Branch Cost'),
                'price' => $companyDetail['Subscription']['branch_cost'],
                'quantity' => $companyDetail['User']['company_branch_count'],
                'total' => ($companyDetail['Subscription']['branch_cost'] * $companyDetail['User']['company_branch_count']),
            ),
            array(
                'particlars' => __('Station Cost'),
                'price' => $companyDetail['Subscription']['setup_cost'],
                'quantity' => $companyDetail['User']['station_file'] * $companyDetail['User']['station_count'],
                'total' => ($companyDetail['User']['station_file'] * $companyDetail['Subscription']['setup_cost'] * $companyDetail['User']['station_count']),
            )
        );
        if ($countInvoice < 2) {
            $billingData = array(
                array(
                    'particlars' => __('Setup Cost'),
                    'price' => $companyDetail['Subscription']['charge'],
                    'quantity' => 1,
                    'total' => $companyDetail['Subscription']['charge'],
                ),
                array(
                    'particlars' => __('Branch Cost'),
                    'price' => $companyDetail['Subscription']['branch_cost'],
                    'quantity' => $companyDetail['User']['company_branch_count'],
                    'total' => ($companyDetail['Subscription']['branch_cost'] * $companyDetail['User']['company_branch_count']),
                ),
                array(
                    'particlars' => __('Station Cost'),
                    'price' => $companyDetail['Subscription']['setup_cost'],
                    'quantity' => $companyDetail['User']['station_file'] * $companyDetail['User']['station_count'],
                    'total' => ($companyDetail['User']['station_file'] * $companyDetail['Subscription']['setup_cost'] * $companyDetail['User']['station_count']),
                )
            );
        }
        $this->set(compact('invoiceDetail', 'companyDetail', 'billingData'));
        $invoiceName = $invoiceDetail['Invoice']['invoice_id'];
        if (empty($invoiceName)) {
            $invoiceName = date('Y_m_d_H_i_s');
        }
        require_once(APP . 'Vendor' . DS . 'dompdf' . DS . 'dompdf_config.inc.php');
        $this->layout = null;
        $output = $this->render();
        $this->response->header(array('Content-type' => 'application/pdf'));
        spl_autoload_register('DOMPDF_autoload');
        $dompdf = new DOMPDF();
        $dompdf->set_paper = 'A4';
        $dompdf->load_html(utf8_decode($output), Configure::read('App.encoding'));
        $dompdf->render();
        file_put_contents(WWW_ROOT . INVOICE_PATH . DS . $invoiceName . '.pdf', $dompdf->output());
        /**
         * sent mail to user for invoice generated
         */
        $invoiceData = array(
            'User' => $companyDetail['User'],
            'Invoice' => $invoiceDetail['Invoice']
        );
        $this->SendEmail->sendInvoiceMail($invoiceData, $companyDetail['User']['role']);
    }

    function dealer_generate_pdf($invoiceId = null, $dealerId = null)
    {
        $this->loadModel('Invoice');
        $this->loadModel('User');
        $this->loadModel('FileProccessingDetail');
        $invoiceDetail = $this->Invoice->find('first', array(
            'contain' => false,
            'conditions' => array('Invoice.id' => $invoiceId)
        ));
        $invoiceData = $this->Invoice->getDealerInvoiceData($invoiceId, $dealerId);
        $totalStationFile = $this->FileProccessingDetail->getStationFileInLastMonth(array('FileProccessingDetail.company_id' => $dealerId));
        $companyDetail = $this->User->find('first', array(
            'fields' => array(
                'id', 'first_name', 'last_name',
                'email', 'phone_no', 'created',
                'name', 'subscription_id',
                'company_branch_count', 'station_count',
                'role'
            ),
            'contain' => array(
                'Subscription' => array(
                    'id', 'setup_cost', 'branch_cost', 'charge', 'type'
                )
            ),
            'conditions' => array(
                'User.id' => $dealerId
            )
        ));
        $companyDetail['User']['station_file'] = $totalStationFile;
        $countInvoice = $this->Invoice->find('count', array('conditions' => array('user_id' => $dealerId)));
        $goldSubscruptionDetail = $this->Subscription->getSubscriptionDetail(GOLD_ID);
        $silverSubscruptionDetail = $this->Subscription->getSubscriptionDetail(SILVER_ID);
        $platSubscruptionDetail = $this->Subscription->getSubscriptionDetail(PLATINUM_ID);

        $billingData = array(
            array(
                'particlars' => __('Silver Company Setup Cost'),
                'price' => $silverSubscruptionDetail['charge'],
                'quantity' => $invoiceData['silverCompany']['count'],
                'total' => ($silverSubscruptionDetail['charge'] * $invoiceData['silverCompany']['count']),
            ),
            array(
                'particlars' => __('Silver Branch Cost'),
                'price' => $silverSubscruptionDetail['branch_cost'],
                'quantity' => $invoiceData['silverCompany']['branch_count'],
                'total' => ($silverSubscruptionDetail['branch_cost'] * $invoiceData['silverCompany']['branch_count']),
            ),
            array(
                'particlars' => __('Silver Station Cost'),
                'price' => $silverSubscruptionDetail['setup_cost'],
                'quantity' => $invoiceData['silverCompany']['station_count'] * $invoiceData['silverCompany']['file_count'],
                'total' => ($silverSubscruptionDetail['setup_cost'] * $invoiceData['silverCompany']['station_count'] * $invoiceData['silverCompany']['file_count']),
            ),
            array(
                'particlars' => __('Gold Company Setup Cost'),
                'price' => $goldSubscruptionDetail['charge'],
                'quantity' => $invoiceData['goldCompany']['count'],
                'total' => ($goldSubscruptionDetail['charge'] * $invoiceData['goldCompany']['count']),
            ),
            array(
                'particlars' => __('Gold Branch Cost'),
                'price' => $goldSubscruptionDetail['branch_cost'],
                'quantity' => $invoiceData['goldCompany']['branch_count'],
                'total' => ($goldSubscruptionDetail['branch_cost'] * $invoiceData['goldCompany']['branch_count']),
            ),
            array(
                'particlars' => __('Gold Station Cost'),
                'price' => $goldSubscruptionDetail['setup_cost'],
                'quantity' => $invoiceData['goldCompany']['station_count'] * $invoiceData['goldCompany']['file_count'],
                'total' => ($goldSubscruptionDetail['setup_cost'] * $invoiceData['goldCompany']['station_count'] * $invoiceData['goldCompany']['file_count']),
            ),
            array(
                'particlars' => __('Platinum Company Setup Cost'),
                'price' => $platSubscruptionDetail['charge'],
                'quantity' => $invoiceData['platCompany']['count'],
                'total' => ($platSubscruptionDetail['charge'] * $invoiceData['platCompany']['count']),
            ),
            array(
                'particlars' => __('Platinum Branch Cost'),
                'price' => $platSubscruptionDetail['branch_cost'],
                'quantity' => $invoiceData['platCompany']['branch_count'],
                'total' => ($platSubscruptionDetail['branch_cost'] * $invoiceData['platCompany']['branch_count']),
            ),
            array(
                'particlars' => __('Platinum Station Cost'),
                'price' => $platSubscruptionDetail['setup_cost'],
                'quantity' => $invoiceData['platCompany']['station_count'] * $invoiceData['platCompany']['file_count'],
                'total' => ($platSubscruptionDetail['setup_cost'] * $invoiceData['platCompany']['station_count'] * $invoiceData['platCompany']['file_count']),
            )
        );
        if ($countInvoice < 2) {
            $billingData = array(
                array(
                    'particlars' => __('Setup Cost'),
                    'price' => $companyDetail['Subscription']['charge'],
                    'quantity' => 1,
                    'total' => $companyDetail['Subscription']['charge'],
                ),
                array(
                    'particlars' => __('Silver Company Setup Cost'),
                    'price' => $silverSubscruptionDetail['charge'],
                    'quantity' => $invoiceData['silverCompany']['count'],
                    'total' => ($silverSubscruptionDetail['charge'] * $invoiceData['silverCompany']['count']),
                ),
                array(
                    'particlars' => __('Silver Branch Cost'),
                    'price' => $silverSubscruptionDetail['branch_cost'],
                    'quantity' => $invoiceData['silverCompany']['branch_count'],
                    'total' => ($silverSubscruptionDetail['branch_cost'] * $invoiceData['silverCompany']['branch_count']),
                ),
                array(
                    'particlars' => __('Silver Station Cost'),
                    'price' => $silverSubscruptionDetail['setup_cost'],
                    'quantity' => $invoiceData['silverCompany']['station_count'] * $invoiceData['silverCompany']['file_count'],
                    'total' => ($silverSubscruptionDetail['setup_cost'] * $invoiceData['silverCompany']['station_count'] * $invoiceData['silverCompany']['file_count']),
                ),
                array(
                    'particlars' => __('Gold Company Setup Cost'),
                    'price' => $goldSubscruptionDetail['charge'],
                    'quantity' => $invoiceData['goldCompany']['count'],
                    'total' => ($goldSubscruptionDetail['charge'] * $invoiceData['goldCompany']['count']),
                ),
                array(
                    'particlars' => __('Gold Branch Cost'),
                    'price' => $goldSubscruptionDetail['branch_cost'],
                    'quantity' => $invoiceData['goldCompany']['branch_count'],
                    'total' => ($goldSubscruptionDetail['branch_cost'] * $invoiceData['goldCompany']['branch_count']),
                ),
                array(
                    'particlars' => __('Gold Station Cost'),
                    'price' => $goldSubscruptionDetail['setup_cost'],
                    'quantity' => $invoiceData['goldCompany']['station_count'] * $invoiceData['goldCompany']['file_count'],
                    'total' => ($goldSubscruptionDetail['setup_cost'] * $invoiceData['goldCompany']['station_count'] * $invoiceData['goldCompany']['file_count']),
                ),
                array(
                    'particlars' => __('Platinum Company Setup Cost'),
                    'price' => $platSubscruptionDetail['charge'],
                    'quantity' => $invoiceData['platCompany']['count'],
                    'total' => ($platSubscruptionDetail['charge'] * $invoiceData['platCompany']['count']),
                ),
                array(
                    'particlars' => __('Platinum Branch Cost'),
                    'price' => $platSubscruptionDetail['branch_cost'],
                    'quantity' => $invoiceData['platCompany']['branch_count'],
                    'total' => ($platSubscruptionDetail['branch_cost'] * $invoiceData['platCompany']['branch_count']),
                ),
                array(
                    'particlars' => __('Platinum Station Cost'),
                    'price' => $platSubscruptionDetail['setup_cost'],
                    'quantity' => $invoiceData['platCompany']['station_count'] * $invoiceData['platCompany']['file_count'],
                    'total' => ($platSubscruptionDetail['setup_cost'] * $invoiceData['platCompany']['station_count'] * $invoiceData['platCompany']['file_count']),
                )
            );
        }

        $this->set(compact('invoiceDetail', 'companyDetail', 'billingData'));
        $invoiceName = $invoiceDetail['Invoice']['invoice_id'];

        if (empty($invoiceName)) {
            $invoiceName = date('Y_m_d_H_i_s');
        }
        require_once(APP . 'Vendor' . DS . 'dompdf' . DS . 'dompdf_config.inc.php');
        $this->layout = null;
        $output = $this->render();
        $this->response->header(array('Content-type' => 'application/pdf'));
        spl_autoload_register('DOMPDF_autoload');
        $dompdf = new DOMPDF();
        $dompdf->set_paper = 'A4';
        $dompdf->load_html(utf8_decode($output), Configure::read('App.encoding'));
        $dompdf->render();
        file_put_contents(WWW_ROOT . INVOICE_PATH . DS . $invoiceName . '.pdf', $dompdf->output());
        /**
         * sent mail to user for invoice generated
         */
        $invoiceData = array(
            'User' => $companyDetail['User'],
            'Invoice' => $invoiceDetail['Invoice']
        );
        $this->SendEmail->sendInvoiceMail($invoiceData, $companyDetail['User']['role']);
    }
}
