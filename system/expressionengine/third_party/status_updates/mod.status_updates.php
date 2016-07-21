<?php

/*
=====================================================
 Status Updates
-----------------------------------------------------
 http://www.intoeetive.com/
-----------------------------------------------------
 Copyright (c) 2012 Yuri Salimovskiy
=====================================================
 This software is intended for usage with
 ExpressionEngine CMS, version 2.0 or higher
=====================================================
 File: mod.status_updates.php
-----------------------------------------------------
 Purpose: Manage status messages for users
=====================================================
*/

if ( ! defined('BASEPATH'))
{
    exit('Invalid file request');
}


class Status_updates {

    var $return_data	= ''; 	
    
    var $settings = array();

    var $perpage = 25;
    
    var $max_length = 760;

    /** ----------------------------------------
    /**  Constructor
    /** ----------------------------------------*/

    function __construct()
    {        
    	$this->EE =& get_instance(); 
		$this->EE->lang->loadfile('status_updates');  
    }
    /* END */
    
    
    
    function display()
    {
    	if ($this->EE->TMPL->fetch_param('member_id')!='' && $this->EE->TMPL->fetch_param('member_id')!=0)
		{
			$member_id = $this->EE->TMPL->fetch_param('member_id');
		}
		else
		{
			$member_id = $this->EE->session->userdata('member_id');
		}
		
		$this->EE->db->start_cache();
		if ($member_id==0 || (!is_numeric($member_id) && $member_id!='ALL'))
		{
			if ($this->EE->TMPL->fetch_param('username')!='')
			{
				$this->EE->db->join('members', 'exp_status_updates.member_id=exp_members.member_id', 'left');
				$this->EE->db->where('username', $this->EE->TMPL->fetch_param('username'));
			}
			else
			if ($this->EE->TMPL->fetch_param('group_id')!='')
			{
				$this->EE->db->join('members', 'exp_status_updates.member_id=exp_members.member_id', 'left');
				$this->EE->db->where('group_id', $this->EE->TMPL->fetch_param('group_id'));
			}
			else
			{
				return $this->EE->TMPL->no_results();
			}
		}
		else if ($member_id!='ALL') 
		{
			$this->EE->db->where('member_id', $member_id);
		}
		$this->EE->db->stop_cache();
    	
    	$start = 0;
        $paginate = ($this->EE->TMPL->fetch_param('paginate')=='top')?'top':(($this->EE->TMPL->fetch_param('paginate')=='both')?'both':'bottom');
        if ($this->EE->TMPL->fetch_param('limit')!='') $this->perpage = $this->EE->TMPL->fetch_param('limit');
        
        $basepath = $this->EE->functions->create_url($this->EE->uri->uri_string);
        $query_string = ($this->EE->uri->page_query_string != '') ? $this->EE->uri->page_query_string : $this->EE->uri->query_string;

		if (preg_match("#^P(\d+)|/P(\d+)#", $query_string, $match) && $this->EE->TMPL->fetch_param('disable')!='pagination')
		{
			$start = (isset($match[2])) ? $match[2] : $match[1];
			$basepath = $this->EE->functions->remove_double_slashes(str_replace($match[0], '', $basepath));
		}

        
        $total = $this->EE->db->count_all_results('exp_status_updates');

		$sort = ($this->EE->TMPL->fetch_param('sort')=='asc')?'asc':'desc';
		$this->EE->db->from('exp_status_updates');
		$this->EE->db->order_by('message_date', $sort);
    	$this->EE->db->limit($this->perpage, $start);
        $query = $this->EE->db->get();
        
        $this->EE->db->flush_cache();
        
        if ($query->num_rows()==0)
        {
            return $this->EE->TMPL->no_results();
        }
        
        $paginate_tagdata = '';
        $tagdata_orig = $this->EE->TMPL->tagdata;
        
        
        if ( preg_match_all("/".LD."paginate".RD."(.*?)".LD."\/paginate".RD."/s", $tagdata_orig, $tmp)!=0)
        {
            $tagdata_orig = str_replace($tmp[0][0], '', $tagdata_orig);
			if ($this->EE->TMPL->fetch_param('disable')!='pagination')
        	{
				$paginate_tagdata = $tmp[1][0];
        	}
        }

        $variables = array();
        $i = 0;
        foreach ($query->result_array() as $row)
        {
			$i++;
			$row['total_updates'] = $total;
			$row['absolute_count'] = $start+$i;
			$variables[] = $row;
        }
        
        $out = $this->EE->TMPL->parse_variables($tagdata_orig, $variables);
        
        if ($this->EE->TMPL->fetch_param('disable')!='pagination')
        {
            $out = $this->_process_pagination($total, $this->perpage, $start, $basepath, $out, $paginate, $paginate_tagdata);		
        }

        return $out;
    	
    }



    function form()
    {
        if ($this->EE->session->userdata('member_id')==0)
        {
            return $this->EE->TMPL->no_results();
        }
        
        
		if ($this->EE->TMPL->fetch_param('return')=='')
        {
            $return = $this->EE->functions->fetch_site_index();
        }
        else if ($this->EE->TMPL->fetch_param('return')=='SAME_PAGE')
        {
            $return = $this->EE->functions->fetch_current_uri();
        }
        else if (strpos($this->EE->TMPL->fetch_param('return'), "http://")!==FALSE || strpos($this->EE->TMPL->fetch_param('return'), "https://")!==FALSE)
        {
            $return = $this->EE->TMPL->fetch_param('return');
        }
        else
        {
            $return = $this->EE->functions->create_url($this->EE->TMPL->fetch_param('return'));
        }
        
        $data['hidden_fields']['ACT'] = $this->EE->functions->fetch_action_id('Status_updates', 'post_update');
		$data['hidden_fields']['RET'] = $return;
        $data['hidden_fields']['PRV'] = $this->EE->functions->fetch_current_uri();
        
        if ($this->EE->TMPL->fetch_param('ajax')=='yes') $data['hidden_fields']['ajax'] = 'yes';
        
        $data['hidden_fields']['maxlength'] = ($this->EE->TMPL->fetch_param('maxlength')!='')?$this->EE->TMPL->fetch_param('maxlength'):$this->max_length;
		
        $tagdata = $this->EE->TMPL->tagdata;
        
        if ($this->EE->TMPL->fetch_param('twitter')=='yes') $data['hidden_fields']['twitter'] = 'yes';
        if ($this->EE->TMPL->fetch_param('facebook')=='yes') $data['hidden_fields']['facebook'] = 'yes';
        if ($this->EE->TMPL->fetch_param('linkedin')=='yes') $data['hidden_fields']['linkedin'] = 'yes';
        
        if (preg_match_all("/".LD."providers.*?(backspace=[\"|'](\d+?)[\"|'])?".RD."(.*?)".LD."\/providers".RD."/s", $tagdata, $matches))
		{
            $slp_installed_q = $this->EE->db->select('settings')->from('modules')->where('module_name','Social_login_pro')->limit(1)->get(); 
	        if ($slp_installed_q->num_rows()==0)
            {
                $tagdata = str_replace($matches[0][0], '', $tagdata);
                continue;
            }
            
            $slp_settings = unserialize($slp_installed_q->row('settings'));
            
            $this->EE->lang->loadfile('social_login_pro');  
            $providers = array('twitter', 'facebook', 'linkedin');

            $out = '';
            $chunk = $matches[3][0];
            $site_id = $this->EE->config->item('site_id');

            foreach ($providers as $provider)
            {
                if ($slp_settings[$site_id]["$provider"]['app_id']!='' && $slp_settings[$site_id]["$provider"]['app_secret']!='' && $slp_settings[$site_id]["$provider"]['custom_field']!='' && (!isset($slp_settings[$site_id]["$provider"]['enable_posts']) || $slp_settings[$site_id]["$provider"]['enable_posts']=='y'))
                {
                    
                    $parsed_chunk = $chunk;
                    $parsed_chunk = $this->EE->TMPL->swap_var_single('provider_name', $provider, $parsed_chunk);
                    $parsed_chunk = $this->EE->TMPL->swap_var_single('provider_title', lang($provider), $parsed_chunk);
                    $parsed_chunk = $this->EE->TMPL->swap_var_single('provider_icon', $this->EE->config->slash_item('theme_folder_url').'third_party/social_login_pro/'.$slp_settings[$site_id]['icon_set'].'/'.$provider.'.png', $parsed_chunk);
 
                    $fieldname = 'm_field_id_'.$slp_settings[$site_id]["$provider"]['custom_field'];
                    $this->EE->db->select($fieldname)
                        ->from('member_data')
                        ->where('member_id', $this->EE->session->userdata('member_id'))
                        ->limit(1);
                    $query = $this->EE->db->get();
                    if ($query->row($fieldname)!='')
                    {
                        $out .= $parsed_chunk;
                    }
                    
                }
            }
            $tagdata = str_replace($matches[0][0], $out, $tagdata);
            
            if ($matches[2][0]!='')
			{
				$tagdata = substr( trim($tagdata), 0, -$matches[2][0]);
			}
			
		}       
        							      
        $data['id']		= ($this->EE->TMPL->fetch_param('id')!='') ? $this->EE->TMPL->fetch_param('id') : 'status_updates_form';
        $data['name']		= ($this->EE->TMPL->fetch_param('name')!='') ? $this->EE->TMPL->fetch_param('name') : 'status_updates_form';
        $data['class']		= ($this->EE->TMPL->fetch_param('class')!='') ? $this->EE->TMPL->fetch_param('class') : 'status_updates_form';
		
		$tagdata = $this->EE->TMPL->swap_var_single('maxlength', $data['hidden_fields']['maxlength'], $tagdata);
		
        $out = $this->EE->functions->form_declaration($data).$tagdata."\n"."</form>";
        
        return $out;
    }
    
    
    function _show_error($ajax, $text)
    {

        $json = array();
        
        if ($ajax)
        {
            $json['result'] = 'error';
            $json['text']   = $text;
            if (function_exists('json_encode'))
            {
                $out = json_encode($json);
            }
            else
            {
                require_once(PATH_THIRD.'social_updates/inc/JSON.php');
                $json_obj = new Services_JSON();
                $out = $json_obj->encode($json);
            }
            echo $out;
            exit();
        }
        $this->EE->output->show_user_error('general', array($text));
        return;
    }
    
    
    function post_update()
    {
        $ajax = ($this->EE->input->get_post('ajax')=='yes')?true:false;
        $return = ($_POST['RET']!='')?$_POST['RET']:$this->EE->functions->fetch_site_index();

        if ($this->EE->session->userdata('member_id')==0)
        {
            return $this->_show_error($ajax, lang('must_be_logged_in'));
        }
        
        if ($this->EE->input->post('message_text')=='')
        {
            return $this->_show_error($ajax, lang('message_empty'));
        }
        
        $maxlength = ($this->EE->input->post('maxlength')>0)?$this->EE->input->post('maxlength'):$this->max_length;
        if (strlen($this->EE->input->post('message_text')>(int) $maxlength))
        {
            return $this->_show_error($ajax, lang('message_too_long'));
        }
        
        $q = $this->EE->db->select('message_text')->from('status_updates')->where('member_id', $this->EE->session->userdata('member_id'))->order_by('message_date', 'desc')->limit(1)->get();
        if ($q->num_rows()>0 && $q->row('message_text')==$this->EE->input->post('message_text'))
        {
            return $this->_show_error($ajax, lang('message_duplicate'));
        }
        
        $insert = array(
			'member_id'     => $this->EE->session->userdata('member_id'),
			'message_date'   => $this->EE->localize->now,
            'message_text'  => $this->EE->input->post('message_text')
		);
		$this->EE->db->insert('status_updates', $insert);
		
        //want to post to social networks?
        if ($this->EE->input->post('twitter')=='yes' || $this->EE->input->post('facebook')=='yes' || $this->EE->input->post('linkedin')=='yes')
        {
            $slp_installed_q = $this->EE->db->select('settings')->from('modules')->where('module_name','Social_login_pro')->limit(1)->get(); 
	        if ($slp_installed_q->num_rows()>0)
            {
                $this->EE->lang->loadfile('social_login_pro');  
				
				$slp_settings = unserialize($slp_installed_q->row('settings'));
                
                $q = $this->EE->db->select('social_login_keys')
                    ->from('members')
                    ->where('member_id', $this->EE->session->userdata('member_id'))->get();
                if ($q->row('social_login_keys')!='')
                {
                    $keys = unserialize($q->row('social_login_keys'));
                    
                    if ($this->EE->input->post('twitter')=='yes')
                    {
                        $this->_slp_post('twitter', $this->EE->input->post('message_text'), $slp_settings, $keys);
                    }
                    
                    if ($this->EE->input->post('facebook')=='yes')
                    {
                        $this->_slp_post('facebook', $this->EE->input->post('message_text'), $slp_settings, $keys);
                    }
                    
                    if ($this->EE->input->post('linkedin')=='yes')
                    {
                        $this->_slp_post('linkedin', $this->EE->input->post('message_text'), $slp_settings, $keys);
                    }

                }
            }
        }
        
        $json = array();
        
        if ($ajax)
        {
            $json['result'] = 'success';
            $json['text']   = $insert['message_text'];
            $json['date']   = $insert['message_date'];
            if (function_exists('json_encode'))
            {
                $out = json_encode($json);
            }
            else
            {
                require_once(PATH_THIRD.'social_updates/inc/JSON.php');
                $json_obj = new Services_JSON();
                $out = $json_obj->encode($json);
            }
            echo $out;
            exit();
        }
        
		$this->EE->functions->redirect($return);
		
    }
    
    
    function _slp_post($provider, $message, $slp_settings, $keys)
    {
        if ( ! class_exists('Social_login_pro_ext'))
    	{
    		require_once PATH_THIRD.'social_login_pro/ext.social_login_pro.php';
    	}
    	
    	$SLP = new Social_login_pro_ext();
        
        $site_id = $this->EE->config->item('site_id');

        if (!isset($keys["$provider"]['oauth_token']) || $keys["$provider"]['oauth_token']=='')
        {
            return;
        }
        if ($slp_settings[$site_id][$provider]['app_id']=='' || $slp_settings[$site_id][$provider]['app_secret']=='' || $slp_settings[$site_id][$provider]['custom_field']=='')
        {
            return;
        }

        if (!isset($slp_settings[$site_id][$provider]['enable_posts']) || $slp_settings[$site_id][$provider]['enable_posts']=='y')
        {
            $msg = $message;
            if (strlen($msg)>$SLP->maxlen[$provider])
            {
                if ( ! class_exists('Shorteen'))
            	{
            		require_once PATH_THIRD.'shorteen/mod.shorteen.php';
            	}
            	
            	$SHORTEEN = new Shorteen();
                
                preg_match_all('/https?:\/\/[^:\/\s]{3,}(:\d{1,5})?(\/[^\?\s]*)?([\?#][^\s]*)?/i', $msg, $matches);

                foreach ($matches as $match)
                {
                    if (!empty($match) && strpos($match[0], 'http')===0)
                    {
                        //truncate urls
                        $longurl = $match[0];
                        if (strlen($longurl)>$SLP->max_link_length)
                        {
                            $shorturl = $SHORTEEN->process($slp_settings[$site_id]['url_shortening_service'], $longurl, true);
                            if ($shorturl!='')
                            {
                                $msg = str_replace($longurl, $shorturl, $msg);
                            }
                        }
                    }
                }
            }
            //still too long? truncate the message
            //at least one URL should always be included
            if (strlen($msg)>$SLP->maxlen[$provider])
            {
                if ($shorturl!='')
                {
                    $len = $SLP->maxlen[$provider] - strlen($shorturl) - 1;
                    $msg = $SLP->_char_limit($msg, $len);
                    $msg .= ' '.$shorturl;
                }
                else
                {
                    $msg = $SLP->_char_limit($msg, $SLP->maxlen[$provider]);
                }
            }
            
            //all is ready! post the message
            $lib = $provider.'_oauth';
            $params = array('key'=>$slp_settings[$site_id]["$provider"]['app_id'], 'secret'=>$slp_settings[$site_id]["$provider"]['app_secret']);
            
			$this->EE->load->add_package_path(PATH_THIRD.'social_login_pro/');
			$this->EE->load->library($lib, $params);
            if ($provider=='yahoo')
            {
                $this->EE->$lib->post($msg, $shorturl, $keys["$provider"]['oauth_token'], $keys["$provider"]['oauth_token_secret'], array('guid'=>$keys["$provider"]['guid']));
            }
            else
            {
                $this->EE->$lib->post($msg, $shorturl, $keys["$provider"]['oauth_token'], $keys["$provider"]['oauth_token_secret']);    
            }
            $this->EE->load->remove_package_path(PATH_THIRD.'social_login_pro/');
        }
    }
    
    

    function _process_pagination($total, $perpage, $start, $basepath='', $out='', $paginate='bottom', $paginate_tagdata='')
    {
        if (version_compare(APP_VER, '2.4.0', '>='))
		{
	        $this->EE->load->library('pagination');
	        if (version_compare(APP_VER, '2.6.0', '>='))
	        {
	        	$pagination = $this->EE->pagination->create(__CLASS__);
	        }
	        else
	        {
	        	$pagination = new Pagination_object(__CLASS__);
	        }
            if (version_compare(APP_VER, '2.8.0', '>='))
            {
                $this->EE->TMPL->tagdata = $pagination->prepare($this->EE->TMPL->tagdata);
                $pagination->build($total, $perpage);
            }
            else
            {
                $pagination->get_template();
    	        $pagination->per_page = $perpage;
    	        $pagination->total_rows = $total;
    	        $pagination->offset = $start;
    	        $pagination->build($pagination->per_page);
            }
	        
	        $out = $pagination->render($out);
  		}
  		else
  		{
        
	        if ($total > $perpage)
	        {
	            $this->EE->load->library('pagination');
	
				$config['base_url']		= $basepath;
				$config['prefix']		= 'P';
				$config['total_rows'] 	= $total;
				$config['per_page']		= $perpage;
				$config['cur_page']		= $start;
				$config['first_link'] 	= $this->EE->lang->line('pag_first_link');
				$config['last_link'] 	= $this->EE->lang->line('pag_last_link');
	
				$this->EE->pagination->initialize($config);
				$pagination_links = $this->EE->pagination->create_links();	
	            $paginate_tagdata = $this->EE->TMPL->swap_var_single('pagination_links', $pagination_links, $paginate_tagdata);			
	        }
	        else
	        {
	            $paginate_tagdata = $this->EE->TMPL->swap_var_single('pagination_links', '', $paginate_tagdata);		
	        }
	        
	        switch ($paginate)
	        {
	            case 'top':
	                $out = $paginate_tagdata.$out;
	                break;
	            case 'both':
	                $out = $paginate_tagdata.$out.$paginate_tagdata;
	                break;
	            case 'bottom':
	            default:
	                $out = $out.$paginate_tagdata;
	        }
	        
    	}
        
        return $out;
    }

    


}
/* END */
?>