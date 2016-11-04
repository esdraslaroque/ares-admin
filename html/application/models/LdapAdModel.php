<?php
class LdapAdModel extends CI_Model {
	
	private $ad_user = 'fencecluster@sefa.pa.gov.br';
	private $ad_pass = '2t6XSHLecuHQdYA4';
	private $ad_host = '10.3.1.24';
	private $ad_port = '389';
	private $ad_basedn = 'DC=sefa,DC=pa,DC=gov,DC=br'; 
	private $ad_connector;
	
	public function __construct() {
		$this->ad_connector = ldap_connect($this->ad_host, $this->ad_port);
		
		if ($this->ad_connector) {
			ldap_set_option($this->ad_connector, LDAP_OPT_REFERRALS, 0);
			ldap_set_option($this->ad_connector, LDAP_OPT_PROTOCOL_VERSION, 3);
			
			if (! ldap_bind($this->ad_connector, $this->ad_user, $this->ad_pass))
				show_error('Ldap Bind failed');
		} else
			show_error('Ldap Connect failed');
	}
	
	public function getMembers($ad_grupo) {
		$query = '(&(objectCategory=user)(memberOf=cn='.$ad_grupo.',OU=Grupos de Seguranca,OU=SEFA-PA,DC=sefa,DC=pa,DC=gov,DC=br))';
		
		$result = ldap_search($this->ad_connector, $this->ad_basedn, $query, array('sAMAccountName', 'displayName', 'userPrincipalName', 'mail'));
		$entries = ldap_get_entries($this->ad_connector, $result);
		
		array_shift($entries);
		
		return $entries;
	}
	
	public function isBlocked($login) {
		$query = '(&(objectCategory=person)(objectClass=user)(userAccountControl:1.2.840.113556.1.4.803:=2)(sAMAccountName='.$login.'))';
		
		$result = ldap_search($this->ad_connector, $this->ad_basedn, $query);
		$num_entries = ldap_count_entries($this->ad_connector, $result);
		
		if ($num_entries == 1)
			return TRUE;
			
		else {
			$query = '(&(objectCategory=person)(objectClass=user)(lockoutTime>=1)(sAMAccountName='.$login.'))';

			$result = ldap_search($this->ad_connector, $this->ad_basedn, $query);
			$num_entries = ldap_count_entries($this->ad_connector, $result);
			
			if ($num_entries == 1)
				return TRUE;
			else
				return FALSE;
		}

		return FALSE;
	}

	public function isValid($login) {
		$query = '(&(objectCategory=person)(objectClass=user)(sAMAccountName='.$login.'))';
	
		$result = ldap_search($this->ad_connector, $this->ad_basedn, $query);
		$num_entries = ldap_count_entries($this->ad_connector, $result);
	
		if ($num_entries == 1)
			return TRUE;
		else
			return FALSE;
	}
	
	public function inAresGroup($login) {
		$query = '(&(objectCategory=user)(memberOf:1.2.840.113556.1.4.1941:=CN=ARES_Users,OU=Grupos de Seguranca,OU=SEFA-PA,DC=sefa,DC=pa,DC=gov,DC=br)(sAMAccountName='.$login.'))';
	
		$result = ldap_search($this->ad_connector, $this->ad_basedn, $query);
		$num_entries = ldap_count_entries($this->ad_connector, $result);
	
		if ($num_entries == 1)
			return TRUE;
		else
			return FALSE;
	}

}