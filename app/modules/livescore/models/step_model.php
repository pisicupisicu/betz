<?php



/**
* Step Model
*
* Manages steps
*
* @author Weblight.ro
* @copyright Weblight.ro
* @package BJ Tool
*/



	class Step_model extends CI_Model

	{

            private $CI;



            function __construct()

            {

                    parent::__construct();



                    $this->CI =& get_instance();

            }

	

            /**
            * Get steps
            * @param array $params
            *
            * @return array
            */

            function get_steps ($params) 

            {

                    $row = array();	

                    $this->db->where('strategy_id',$params['strategy_id']);
                    if (isset($params['amount'])) $this->db->where('amount >=',$params['amount']);

                    if (isset($params['limit'])) {

                                $offset = (isset($params['offset'])) ? $params['offset'] : 0;

                                $this->db->limit($params['limit'], $offset);

                        }

                    $result = $this->db->get('z_steps');



                    foreach ($result->result_array() as $linie) {

                            $row[] = $linie;

                    }



                    return $row;														

            }

                

            /**
            * Get Step
            *
            * @param int $id	
            *
            * @return array
            */

            function get_step ($id) 

            {

                    $row = array();								

                    $this->db->where('id',$id);

                    $result = $this->db->get('z_steps');



                    foreach ($result->result_array() as $row) {

                            return $row;

                    }



                    return $row;														

            }



            /**
            * Create New Steps
            *
            * Creates a new step
            *
            * @param array $insert_fields	
            *
            * @return int $insert_id
            */

            function new_step ($insert_fields) 

            {																					

                    $this->db->insert('z_steps', $insert_fields);		

                    $insert_id = $this->db->insert_id();



                    return $insert_id;

            }



            /**
            * Update Step
            *
            * Updates step
            * 
            * @param array $update_fields
            * @param int $id	
            *
            * @return boolean TRUE
            */

            function update_step ($update_fields,$id) 

            {		

                    $this->db->update('z_steps',$update_fields,array('id' => $id));



                    return TRUE;

            }



            /**
            * Delete step
            *
            * Deletes step
            * 	
            * @param int $id	
            *
            * @return boolean TRUE
            */

            function delete_step ($id) 

            {		

                $this->db->delete('z_steps',array('id' => $id));



                return TRUE;

            }

            

            function delete_strategy_steps($strategy_id)

            {

                $this->db->delete('z_steps',array('strategy_id' => $strategy_id));

                

                return TRUE;

            }

            

            function get_num_rows($strategy_id)

            {

                $this->db->where('strategy_id',$strategy_id);

                $result = $this->db->get('z_steps');

                return $result->num_rows();        

            }

}

