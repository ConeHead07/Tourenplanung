<?php

/**
 * 
 * formoptions
 * @author rybka
 * @abstract 	
 * This option is valid only in form editing. 
 * The purpose of these options is to reorder the elements in the form 
 * and to add some information before and after the editing element. 
 * Should be used in colModel array. Syntax:
 *
 *
 * 	<script>
 * 	jQuery("#grid_id").jqGrid({
 * 		...
 * 		colModel: [
 * 		...
 * 		{
 * 			name:'price', ..., formoptions:{
 * 				elmprefix:'(*)', rowpos:1, colpos:2....}, editable:true },
 * 				...
 * 				]
 * 				...
 * 	});
 * 	</script>
 *
 * 	If you plan to use this object in collModel with rowpos and colpos properties it is recommended that all editing fields use these properties.
 *
 *
 */
class JqGridColModelFormOptions extends JqGridBaseObject
{
		
	/**
	*
	* @abstract if set, a text or html content appears before the input element
	* @param
	* @return JqGridColModelFormOptions
	*/
	public function set_elmprefix($param)
	{
		$this->params->elmprefix = $param; return $this;
	}
	
	/**
	 *
	 * @abstract if set, a text or html content appears after the input element
	 * @param
	 * @return JqGridColModelFormOptions
	 */
	public function set_elmsuffix($param)
	{
		$this->params->elmsuffix = $param; return $this;
	}
	
	/**
	 * if set, this replace the name from colNames array that appears as label in the f
	 * @abstract if set, this replace the name from colNames array that appears as label in the form.<br>
	 *
	 * @param
	 * @return JqGridColModelFormOptions
	 */
	public function set_label($param)
	{
		$this->params->label = $param; return $this;
	}
	
	/**
	 *
	 * @abstract determines the row position of the element (again with the text-label) in the form; the count begins from 1
	 * @param
	 * @return JqGridColModelFormOptions
	 */
	public function set_rowpos($param)
	{
		$this->params->rowpos = $param; return $this;
	}
	
	/**
	 *
	 * @abstract determines the column position of the element (again with thelabel) in the form beginning from 1
	 * @param
	 * @return JqGridColModelFormOptions
	 */
	public function set_colpos($param)
	{
		$this->params->colpos = $param; return $this;
	}
}