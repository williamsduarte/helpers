<?php

namespace Vialoja\Helpers;

/*
* 2014 ViaLoja Shopping
* @author ViaLoja Shopping <contato@vialoja.com.br>
* @copyright  2014 ViaLoja Shopping
*/

/**
 * Api email
 * http://docs.cpanel.net/twiki/bin/view/ApiDocs/Api2/ApiEmail
 *
 * Api subdominios
 * http://docs.cpanel.net/twiki/bin/view/ApiDocs/Api2/ApiSubDomain
 *
 * Funções dominios
 * http://docs.cpanel.net/twiki/bin/vief/ApiDocs/Api2/ApiPark
 *
 **/

class ManipulateXmlapi
{

    private $host;
    private $user;
    private $password;
    private $email_user;
    private $email_password;
    private $email_quota;
    private $email_domain;
    private $name_sub_domain = null;
    private $name_domain = null;
    private $dir;
    public $status = false;
    private $xmlapi;
    private $result;

    public function __construct($host, $user, $password)
    {

        $this->host = $host;
        $this->user = $user;
        $this->password = $password;

        try {

            if (empty($this->host)) {
                throw new \LogicException("Valor obrigatório ManipulateXmlapi: Informe o host.", E_USER_NOTICE);
            }

            if (empty($this->user)) {
                throw new \LogicException("Valor obrigatório ManipulateXmlapi: Informe o user.", E_USER_NOTICE);
            }

            if (empty($this->password)) {
                throw new \LogicException("Valor obrigatório ManipulateXmlapi: Informe o password.", E_USER_NOTICE);
            }

        } catch (\LogicException $e) {
            exit(sprintf('Erro: Mensagem: %s Class: %s Function: %s', $e->getMessage()));
        }

        $this->xmlapi = new \xmlapi($host);
        $this->xmlapi->set_port(2083);
        $this->xmlapi->password_auth($this->user, $this->password);
        $this->xmlapi->set_output('json');
        $this->xmlapi->set_debug(1);

        if (!defined('ERROR_PROCESS')) {
            define('ERROR_PROCESS', '<b>Atenção: </b>Houve um erro no processamento do pedido.');
        }

    }

    public function setEmailUser($email_user)
    {
        $this->email_user = $email_user;
    }

    public function setEmailPassword($email_password)
    {
        $this->email_password = $email_password;
    }

    public function setEmailQuota($email_quota)
    {
        $email_quota = intval($email_quota);
        if ($email_quota <= 249) {
            throw new \Exception("Informe a quota mínima de 250Mb.", E_USER_NOTICE);
        }
        $this->email_quota = $email_quota;
    }

    public function setEmailDomain($email_domain)
    {
        $this->email_domain = $email_domain;
    }

    public function setNameSubDomain($name_sub_domain)
    {
        $this->name_sub_domain = $name_sub_domain;
    }

    public function setNameDomain($name_domain)
    {
        $this->name_domain = $name_domain;
    }

    public function setDirectory($dir)
    {
        $this->dir = $dir;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function deleteDns()
    {

        return $this->xmlapi->gethostname();
    }

    /**
     * Deleta o email da conta
     * @throws \Exception
     */
    public function addEmail()
    {

        try {

            if (empty($this->email_user)) {
                throw new \LogicException("O email deve ser informado!", E_USER_NOTICE);
            }

            if (empty($this->email_password)) {
                throw new \LogicException("A senha do email deve ser informado!", E_USER_NOTICE);
            }

            if (empty($this->email_domain)) {
                throw new \LogicException("O domínio do email deve ser informado!", E_USER_NOTICE);
            }

            if (empty($this->email_quota)) {
                throw new \LogicException("A quota do email deve ser informado!", E_USER_NOTICE);
            }

            $this->result = $this->xmlapi->api2_query($this->user, "Email", "addpop",
                array(
                    'domain' => $this->email_domain,
                    'email' => $this->email_user,
                    'password' => $this->email_password,
                    'quota' => $this->email_quota
                )
            );


            if ($this->result === (bool)"") {
                throw new \UnderflowException(ERROR_PROCESS);
            }

            $this->result = json_decode($this->result);

            if (isset($this->result->cpanelresult->error)) {
                throw new \DomainException($this->result->cpanelresult->error);
            } else {
                $this->status = true;
            }

        } catch (\UnderflowException $e) {

            throw new \Exception($e->getMessage());

        } catch (\DomainException $e) {

            throw new \Exception($e->getMessage());

        } catch (\LogicException $e) {

            \Exception\VialojaInvalidLogicException::errorHandler($e);

        }

    }

    /**
     * Deleta o email da conta
     * @throws \Exception
     */
    public function delEmail()
    {


        try {

            if (empty($this->email_user)) {
                throw new \LogicException("O email deve ser informado!", E_USER_NOTICE);
            }

            if (empty($this->email_domain)) {
                throw new \LogicException("O domínio do email deve ser informado!", E_USER_NOTICE);
            }

            $this->result = $this->xmlapi->api2_query($this->user, "Email", "delpop",
                array(
                    'domain' => $this->email_domain,
                    'email' => $this->email_user
                )
            );

            if ($this->result === (bool)"") {
                throw new \UnderflowException();
            }

            $this->result = json_decode($this->result);


            if (isset($this->result->cpanelresult->error)) {
                throw new \DomainException($this->result->cpanelresult->error);
            } else {
                $this->status = true;
            }

        } catch (\UnderflowException $e) {

            throw new \Exception($e->getMessage());

        } catch (\DomainException $e) {

            throw new \Exception($e->getMessage());

        } catch (\LogicException $e) {

            \Exception\VialojaInvalidLogicException::errorHandler($e);

        }

    }


    /**
     * Altera a quota do email da conta
     * @throws \Exception
     */
    public function editQuotaEmail()
    {

        try {

            if (empty($this->email_user)) {
                throw new \LogicException("O email deve ser informado!", E_USER_NOTICE);
            }

            if (empty($this->email_quota)) {
                throw new \LogicException("A quota do email deve ser informado!", E_USER_NOTICE);
            }

            if (empty($this->email_domain)) {
                throw new \LogicException("O domínio do email deve ser informado!", E_USER_NOTICE);
            }

            if (empty($this->email_quota)) {
                throw new \LogicException("A quota do email deve ser informado!", E_USER_NOTICE);
            }

            $this->result = $this->xmlapi->api2_query($this->user, "Email", "editquota",
                array(
                    'domain' => $this->email_domain,
                    'email' => $this->email_user,
                    'quota' => $this->email_quota
                )
            );

            if ($this->result === (bool)"") {
                throw new \UnderflowException();
            }

            $this->result = json_decode($this->result);

            if (isset($this->result->cpanelresult->error)) {
                throw new \DomainException($this->result->cpanelresult->error);
            } else {
                $this->status = true;
            }

        } catch (\UnderflowException $e) {

            throw new \Exception($e->getMessage());

        } catch (\DomainException $e) {

            throw new \Exception($e->getMessage());

        } catch (\LogicException $e) {

            \Exception\VialojaInvalidLogicException::errorHandler($e);

        }

    }


    /**
     * Altera senha do email da conta
     * @throws \Exception
     */
    public function editPassEmail()
    {


        try {

            if (empty($this->email_user)) {
                throw new \LogicException("O email deve ser informado!", E_USER_NOTICE);
            }

            if (empty($this->email_password)) {
                throw new \LogicException("A senha do email deve ser informado!", E_USER_NOTICE);
            }

            if (empty($this->email_domain)) {
                throw new \LogicException("O domínio do email deve ser informado!", E_USER_NOTICE);
            }

            $this->result = $this->xmlapi->api2_query($this->user, "Email", "passwdpop",
                array(
                    'domain' => $this->email_domain,
                    'email' => $this->email_user,
                    'password' => $this->email_password
                )
            );


            if ($this->result === (bool)"") {
                throw new \UnderflowException();
            }

            $this->result = json_decode($this->result);

            if (isset($this->result->cpanelresult->error)) {
                throw new \DomainException($this->result->cpanelresult->error);
            } else {
                $this->status = true;
            }

        } catch (\DomainException $e) {

            throw new \Exception($e->getMessage(), E_USER_NOTICE);

        } catch (\UnderflowException $e) {

            throw new \Exception(ERROR_PROCESS);

        } catch (\LogicException $e) {

            \Exception\VialojaInvalidLogicException::errorHandler($e);

        }

    }


    /**
     * Lista os emails
     * @return array|mixed|object
     */
    public function listEmails()
    {

        try {

            $this->result = $this->xmlapi->api2_query($this->user, 'Email', 'listpopswithdisk');

            if ($this->result === (bool)"") {
                throw new \UnderflowException();
            }

            return json_decode($this->result);

        } catch (\UnderflowException $e) {

        }

    }

    /**
     * Cadastra um novo subdomínio
     * @throws \Exception
     */
    public function addSubDomain()
    {

        try {

            if (empty($this->name_sub_domain)) {
                throw new \LogicException("O Subdomínio deve ser informado!", E_USER_NOTICE);
            }

            if (empty($this->dir)) {
                throw new \LogicException("O diretorio deve ser informado!", E_USER_NOTICE);
            }

            if (strpos($this->dir, '/public_html') === false) {
                throw new \LogicException("O diretorio <b>/public_html</b> e a barra antes deve ser informado!", E_USER_NOTICE);
            }

            $this->result = $this->xmlapi->api2_query($this->user, 'SubDomain', 'addsubdomain',
                array(
                    'dir' => $this->dir,
                    'disallowdot' => 0,
                    'domain' => $this->name_sub_domain,
                    'rootdomain' => $this->name_domain
                )
            );


            if ($this->result === (bool)"") {
                throw new \UnderflowException();
            }

            $this->result = json_decode($this->result);

            if (isset($this->result->cpanelresult->error)) {
                throw new \DomainException($this->result->cpanelresult->error);
            } else {
                $this->status = true;
            }

        } catch (\UnderflowException $e) {

            throw new \Exception($e->getMessage());

        } catch (\DomainException $e) {

            throw new \Exception($e->getMessage());

        } catch (\LogicException $e) {

            \Exception\VialojaInvalidLogicException::errorHandler($e);

        }

    }


    /**
     * Deleta o subdomínio
     * @throws \Exception
     */
    public function delSubDomain()
    {

        try {

            if (empty($this->name_sub_domain)) {
                throw new \LogicException("O Subdomínio deve ser informado!", E_USER_NOTICE);
            }

            $this->result = $this->xmlapi->api2_query($this->user, 'SubDomain', 'delsubdomain',
                array(
                    'dir' => $this->dir,
                    'domain' => $this->name_sub_domain,
                    'rootdomain' => $this->name_domain
                )
            );

            if ($this->result === (bool)"") {
                throw new \UnderflowException();
            }

            $this->result = json_decode($this->result);

            if (isset($this->result->cpanelresult->error)) {
                throw new \DomainException($this->result->cpanelresult->error);
            } else {
                $this->status = true;
            }

        } catch (\UnderflowException $e) {

            throw new \Exception($e->getMessage());

        } catch (\DomainException $e) {

            throw new \Exception($e->getMessage());

        } catch (\LogicException $e) {

            \Exception\VialojaInvalidLogicException::errorHandler($e);

        }

    }


    /**
     * Muda o diretorio do subdomínio
     * @throws \Exception
     */
    public function changeSubDomain()
    {

        try {

            if (empty($this->name_sub_domain)) {
                throw new \LogicException("O Subdomínio deve ser informado!", E_USER_NOTICE);
            }

            if (empty($this->dir)) {
                throw new \LogicException("O diretorio deve ser informado!", E_USER_NOTICE);
            }

            if (strpos($this->dir, '/public_html') === false) {
                throw new \LogicException("O diretorio <b>/public_html</b> e a barra antes deve ser informado!", E_USER_NOTICE);
            }

            $this->result = $this->xmlapi->api2_query($this->user, 'SubDomain', 'changedocroot',
                array(
                    'rootdomain' => $this->name_domain,
                    'dir' => $this->dir,
                    'subdomain' => $this->name_sub_domain
                )
            );

            if ($this->result === (bool)"") {
                throw new \UnderflowException();
            }

            $this->result = json_decode($this->result);

            if (isset($this->result->cpanelresult->error)) {
                throw new \DomainException($this->result->cpanelresult->error);
            } else {
                $this->status = true;
            }

        } catch (\UnderflowException $e) {

            throw new \Exception($e->getMessage());

        } catch (\DomainException $e) {

            throw new \Exception($e->getMessage());

        } catch (\LogicException $e) {

            \Exception\VialojaInvalidLogicException::errorHandler($e);

        }

    }

    /**
     * Lista os subdomínios
     * @return array|mixed|object
     * @throws \Exception
     */
    public function allSubDomains()
    {

        try {

            $this->result = $this->xmlapi->api2_query($this->user, 'SubDomain', 'listsubdomains');

            if ($this->result === (bool)"") {
                throw new \UnderflowException();
            }

            return json_decode($this->result);

        } catch (\UnderflowException $e) {

            throw new \Exception($e->getMessage(), E_USER_NOTICE);

        }

    }

    /**
     * Cadastra novo domínio em domínios estacionados
     * @throws \Exception
     */
    public function addDomainParket()
    {

        try {

            if (empty($this->name_domain)) {
                throw new \LogicException("O domínio deve ser informado!", E_USER_NOTICE);
            }

            $this->result = $this->xmlapi->park($this->user, $this->name_domain, $this->name_sub_domain);

            if ($this->result === (bool)"") {
                throw new \UnderflowException();
            }

            $this->result = json_decode($this->result);

            if (isset($this->result->cpanelresult->error)) {
                throw new \DomainException($this->result->cpanelresult->error);
            } else {
                $this->status = true;
            }

        } catch (\UnderflowException $e) {

            throw new \Exception($e->getMessage());

        } catch (\DomainException $e) {

            throw new \Exception($e->getMessage());

        } catch (\LogicException $e) {

            \Exception\VialojaInvalidLogicException::errorHandler($e);

        }

    }

    /**
     * Remove um domínio de domínios estacionados
     * @throws \Exception
     */
    public function delDomainParket()
    {

        try {

            if (empty($this->name_domain)) {
                throw new \LogicException("O domínio deve ser informado!", E_USER_NOTICE);
            }

            $this->result = $this->xmlapi->unpark($this->user, $this->name_domain);

            if ($this->result === (bool)"") {
                throw new \UnderflowException();
            }

            $this->result = json_decode($this->result);

            if (isset($this->result->cpanelresult->error)) {
                throw new \DomainException($this->result->cpanelresult->error);
            } else {
                $this->status = true;
            }

        } catch (\UnderflowException $e) {

            throw new \Exception($e->getMessage());

        } catch (\DomainException $e) {

            throw new \Exception($e->getMessage());

        } catch (\LogicException $e) {

            \Exception\VialojaInvalidLogicException::errorHandler($e);

        }

    }

    /**
     * Lista os domínios suplementares
     * @return array|mixed|object
     * @throws \Exception
     */
    public function allAddonDomains()
    {

        try {

            $this->result = $this->xmlapi->api2_query($this->user, 'Park', 'listaddondomains');

            if ($this->result === (bool)"") {
                throw new \UnderflowException();
            }

            return json_decode($this->result);

        } catch (\UnderflowException $e) {

            throw new \Exception($e->getMessage(), E_USER_NOTICE);

        }

    }

    /**
     * Lista os domínios estacionados
     * @return array|mixed|object
     * @throws \Exception
     */
    public function allParkedDomains()
    {

        try {

            $this->result = $this->xmlapi->api2_query($this->user, 'Park', 'listparkeddomains');

            if ($this->result === (bool)"") {
                throw new \UnderflowException();
            }

            return json_decode($this->result);

        } catch (\UnderflowException $e) {

            throw new \Exception($e->getMessage(), E_USER_NOTICE);

        }

    }

}
