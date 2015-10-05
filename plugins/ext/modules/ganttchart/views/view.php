<?php
  if(isset($_GET['path']))
  { 
    $path_info = items::parse_path($_GET['path']);
    $current_path = $_GET['path']; 
    $current_entity_id = $path_info['entity_id'];
    $current_item_id = true; // set to true to set off default title     
    $current_path_array = $path_info['path_array'];
    $app_breadcrumb = items::get_breadcrumb($current_path_array);
    
    require(component_path('items/navigation'));
  } 
?>
<h3 class="page-title"><?php echo $reports['name'] ?></h3>

<div class="jqueryGantt">
  <div id="workSpace" style="padding:0px; overflow-y:auto; overflow-x:hidden;border:1px solid #e5e5e5;position:relative;margin:0 5px"></div>
</div>

<div id="taZone" style="display:none;" class="noprint">
   <textarea rows="8" cols="150" id="ta" style="display:none">
    <?php echo json_encode($ganttcahrt_data) ?>
    <?php //echo $str;?>
   </textarea>  
</div>


<script type="text/javascript">

var ge;  //this is the hugly but very friendly global var for the gantt editor
$(function() {

  //load templates
  $("#ganttemplates").loadTemplates();
  
  var cut_width = 270;
  if($('body').hasClass("page-sidebar-closed")) var cut_width = 70;
  
  // here starts gantt initialization
  ge = new GanttMaster();
  var workSpace = $("#workSpace");
  workSpace.css({width:$(window).width() - cut_width,height:$(window).height() - 300});
  ge.init(workSpace);



  $(".ganttButtonBar h1").html("");
  $(".ganttButtonBar div").addClass('buttons');
  
  loadI18n();

  //simulate a data load from a server.
  loadGanttFromServer();


  function gantt_resize()
  {
    var cut_width = 270;
    if($('body').hasClass("page-sidebar-closed")) var cut_width = 70;
    if($(window).width()<990) var cut_width = 50;
        
    workSpace.css({width:$(window).width() - cut_width,height:$(window).height() - 300});
    workSpace.trigger("resize.gantt");
  }

  $(window).resize(function(){
    //alert($(window).height())
    gantt_resize()    
  }).oneTime(150,"resize",function(){$(this).trigger("resize")});
  
  //check info icon
  $('.gantt_item_info_icon').each(function(){     
     if($(this).attr('rel')!='0')
     {      
       $(this).css('display','inline-block');
     }
  });
  
  
  //set off edit for first row
  $('.taskEditRow').each(function(){
    if($(this).attr('level')==0)
    {
      $('input',this).attr('readonly','readonly')
    }
  })
  
  $('.page-sidebar').on('click', '.sidebar-toggler', function (e) {
    setTimeout(function() { gantt_resize() }, 100);    
  });
  
});


function loadGanttFromServer(taskId, callback) {

  //this is a simulation: load data from the local storage if you have already played with the demo or a textarea with starting demo data
  loadFromLocalStorage();

}

function loadI18n() {
  GanttMaster.messages = {
    "CANNOT_WRITE":       "CANNOT WRITE",
    "CHANGE_OUT_OF_SCOPE" : "NO RIGHTS FOR_UPDATE PARENTS OUT OF EDITOR SCOPE",
    "START_IS_MILESTONE" : "START IS MILESTONE",
    "END_IS_MILESTONE" : "END IS MILESTONE",
    "TASK_HAS_CONSTRAINTS" : "TASK HAS CONSTRAINTS",
    "GANTT_ERROR_DEPENDS_ON_OPEN_TASK" : "GANTT ERROR DEPENDS ON OPEN TASK",
    "GANTT_ERROR_DESCENDANT_OF_CLOSED_TASK" : "GANTT ERROR DESCENDANT OF CLOSED TASK",
    "TASK_HAS_EXTERNAL_DEPS" : "TASK HAS EXTERNAL DEPS",
    "GANTT_ERROR_LOADING_DATA_TASK_REMOVED" : "GANTT ERROR LOADING DATA TASK REMOVED",
    "ERROR_SETTING_DATES" : "ERROR SETTING DATES",
    "CIRCULAR_REFERENCE" : "<?php echo TEXT_EXT_GANTT_CIRCULAR_REFERENCE?>",
    "CANNOT_DEPENDS_ON_ANCESTORS" : "<?php echo TEXT_EXT_GANTT_CANNOT_DEPENDS ?>",
    "CANNOT_DEPENDS_ON_DESCENDANTS" : "<?php echo TEXT_EXT_GANTT_CANNOT_DEPENDS ?>",
    "INVALID_DATE_FORMAT" : "<?php echo TEXT_EXT_GANTT_INVALID_DATE_FORMAT ?>",
    "TASK_MOVE_INCONSISTENT_LEVEL" : "TASK MOVE INCONSISTENT LEVEL",

    "GANTT_QUARTER_SHORT" : "<?php echo TEXT_EXT_GANTT_QUARTER_SHORT ?>",
    "GANTT_SEMESTER_SHORT" : "<?php echo TEXT_EXT_GANTT_SEMESTER_SHORT ?>",
    "DELETE_NOT_ALLOWED": "<?php echo TEXT_EXT_GANTT_DELETE_NOT_ALLOWED ?>"
  };
}


function saveGanttOnServer() {
  if(!ge.canWrite)
    return;


  //this is a simulation: save data to the local storage or to the textarea
  saveInLocalStorage();

}

function clearGantt() {
  ge.reset();
}


//-------------------------------------------  LOCAL STORAGE MANAGEMENT (for this demo only) ------------------------------------------------------
Storage.prototype.setObject = function(key, value) {
  this.setItem(key, JSON.stringify(value));
};


Storage.prototype.getObject = function(key) {
  return this.getItem(key) && JSON.parse(this.getItem(key));
};


function loadFromLocalStorage() {
  var ret;
  
  $("#taZone").show();
  
  if (!ret || !ret.tasks || ret.tasks.length == 0)
  {
    ret = JSON.parse($("#ta").val());
  }
  
  ge.loadProject(ret);
  ge.checkpoint(); //empty the undo stack
}


function saveInLocalStorage() {
  var prj = ge.saveProject();
  
  $("#ta").val(JSON.stringify(prj));
  
  $('#text_saving').css('display','inline');
  
  $.ajax({type: "POST",
          url: '<?php echo url_for("ext/ganttchart/save_ganttcahrt","id=" . $reports["id"])?>',
          data: {entities_id:'<?php echo $reports["name"] ?>',path: '<?php echo (isset($_GET["path"]) ? $_GET["path"]:"") ?>',ganttcahrt_data:JSON.stringify(prj)}}).done(function() {
            location.href='<?php echo url_for("ext/ganttchart/view","id=" . $reports["id"] . (isset($_GET["path"]) ? "&path=" . $_GET["path"] : "" ))?>';
          });
  
}


//-------------------------------------------  Open a black popup for managing resources. This is only an axample of implementation (usually resources come from server) ------------------------------------------------------


</script>


<?php if($users_has_full_access){ ?>
<div id="gantEditorTemplates" style="display:none;">
  <div class="__template__" type="GANTBUTTONS"><!--
  <div class="ganttButtonBar noprint">
    <h1 style="float:left">task tree/gantt</h1>
    <div class="buttons">
    <button onclick="$('#workSpace').trigger('undo.gantt');" class="button textual" title="<?php echo TEXT_UNDO ?>"><span class="teamworkIcon">&#39;</span></button>
    <button onclick="$('#workSpace').trigger('redo.gantt');" class="button textual" title="<?php echo TEXT_REDO ?>"><span class="teamworkIcon">&middot;</span></button>
    <span class="ganttButtonSeparator"></span>
    <button onclick="$('#workSpace').trigger('addAboveCurrentTask.gantt');" class="button textual" title="<?php echo TEXT_INSERT ?>"><span class="teamworkIcon">l</span></button>            
    <span class="ganttButtonSeparator"></span>
    <button onclick="$('#workSpace').trigger('moveUpCurrentTask.gantt');" class="button textual" title="<?php echo TEXT_MOVE_UP ?>"><span class="teamworkIcon">k</span></button>
    <button onclick="$('#workSpace').trigger('moveDownCurrentTask.gantt');" class="button textual" title="<?php echo TEXT_MOVE_DOWN ?>"><span class="teamworkIcon">j</span></button>
    <span class="ganttButtonSeparator"></span>
    <button onclick="$('#workSpace').trigger('zoomMinus.gantt');" class="button textual" title="<?php echo TEXT_ZOOM_OUT ?>"><span class="teamworkIcon">)</span></button>
    <button onclick="$('#workSpace').trigger('zoomPlus.gantt');" class="button textual" title="<?php echo TEXT_ZOOM_IN ?>"><span class="teamworkIcon">(</span></button>
    <span class="ganttButtonSeparator"></span>
    <button onclick="$('#workSpace').trigger('deleteCurrentTask.gantt');" class="button textual" title="<?php echo TEXT_DELETE ?>"><span class="teamworkIcon">&cent;</span></button>  
    <span class="ganttButtonSeparator"></span>
    <button onclick="print();" class="button textual" title="<?php echo TEXT_PRINT ?>"><span class="teamworkIcon">p</span></button>    
      &nbsp; &nbsp; &nbsp; &nbsp;
      <button onclick="saveGanttOnServer();" class="btn btn-primary"><?php echo TEXT_BUTTON_SAVE ?></button>
      <span id="text_saving" style="display:none"><?php echo TEXT_SAVING ?></span>
    </div></div>
  --></div>
<?php  }else{ ?>

<div id="gantEditorTemplates" style="display:none;">
  <div class="__template__" type="GANTBUTTONS"><!--
  <div class="ganttButtonBar noprint">
    <h1 style="float:left">task tree/gantt</h1>
    <div class="buttons">

    <button onclick="$('#workSpace').trigger('zoomMinus.gantt');" class="button textual" title="zoom out"><span class="teamworkIcon">)</span></button>
    <button onclick="$('#workSpace').trigger('zoomPlus.gantt');" class="button textual" title="zoom in"><span class="teamworkIcon">(</span></button>      
    <span class="ganttButtonSeparator"></span>
    <button onclick="print();" class="button textual" title="print"><span class="teamworkIcon">p</span></button>    
          
    </div></div>
  --></div>

<?php } ?>  

  <div class="__template__" type="TASKSEDITHEAD"><!--
  <table class="gdfTable" cellspacing="0" cellpadding="0">
    <thead>
    <tr style="height:40px">
      <th class="gdfColHeader" style="width:35px;"></th>
          
      <th class="gdfColHeader gdfResizable" style="width:300px;"><?php echo TEXT_NAME ?></th>
      
<?php
  if(strlen($reports['fields_in_listing'])>0)
  {     
                 
    $fields_query = db_query("select *,if(length(short_name)>0,short_name,name) as name from app_fields where entities_id='" . $reports['entities_id'] . "' and id in (" . $reports['fields_in_listing']  . ") order by field(id," . $reports['fields_in_listing'] . ")");
    while($field = db_fetch_array($fields_query))
    {
      //check field access
      if(isset($fields_access_schema[$field['id']]))
      {
        if($fields_access_schema[$field['id']]=='hide') continue;
      }
      
      echo '<th class="gdfColHeader gdfResizable" style="width:70px;">' . fields_types::get_option($field['type'],'name',$field['name']) . '</th>';
    }
  } 
?>      
      <th class="gdfColHeader gdfResizable" style="width:80px;"><?php echo TEXT_EXT_GANTT_START_DATE_SHORT ?></th>
      <th class="gdfColHeader gdfResizable" style="width:80px;"><?php echo TEXT_EXT_GANTT_END_DATE_SHORT ?></th>
      <th class="gdfColHeader gdfResizable" style="width:50px;"><?php echo TEXT_EXT_GANTT_DURATION_SHORT ?></th>
      <th class="gdfColHeader gdfResizable" style="width:50px;"><?php echo TEXT_EXT_GANTT_DEPENDENCE_SHORT ?></th>
      
      
    </tr>
    </thead>
  </table>
  --></div>

  <div class="__template__" type="TASKROW"><!--
  <tr taskId="(#=obj.id#)" class="taskEditRow" level="(#=level#)">
    <th class="gdfCell" align="right" style="cursor:pointer;">
      <span class="taskRowIndex">(#=obj.getRow()+1#)</span>       
      <a rel="(#=obj.id#)" href="<?php echo url_for('items/info','path=' . $reports['entities_id']) ?>-(#=obj.id#)" target="_blank" class="teamworkIcon gantt_item_info_icon" style="font-size:12px; display:none; text-decoration:none">(</a>
      </th>
            
    <td class="gdfCell indentCell" style="padding-left:(#=obj.level*10#)px;">
      <div class="(#=obj.isParent()?'exp-controller expcoll exp':'exp-controller'#)" align="center"></div>
      <input type="text" name="name" value="(#=obj.name#)" >
    </td>

<?php
  if(strlen($reports['fields_in_listing'])>0)
  {                  
    $fields_query = db_query("select * from app_fields where entities_id='" . $reports['entities_id'] . "' and id in (" . $reports['fields_in_listing']  . ") order by field(id," . $reports['fields_in_listing'] . ")");
    while($field = db_fetch_array($fields_query))
    {
      //check field access
      if(isset($fields_access_schema[$field['id']]))
      {
        if($fields_access_schema[$field['id']]=='hide') continue;
      }
      
      echo '<td class="gdfCell">(#=obj.field_' . $field['id'] . '#)</td>';
    }
  } 
?>         
    <td class="gdfCell"><input type="text" name="start"  value="" class="date"></td>
    <td class="gdfCell"><input type="text" name="end" value="" class="date"></td>
    <td class="gdfCell"><input type="text" name="duration" value="(#=obj.duration#)"></td>
    <td class="gdfCell"><input type="text" name="depends" value="(#=obj.depends#)" (#=obj.hasExternalDep?"readonly":""#)></td>
        
  </tr>
  --></div>

  <div class="__template__" type="TASKEMPTYROW"><!--
  <tr class="taskEditRow emptyRow" >    
    <th class="gdfCell"></th>
    <td class="gdfCell"></td>
    <td class="gdfCell"></td>
    <td class="gdfCell"></td>
    <td class="gdfCell"></td>    
    <td class="gdfCell"></td>
    
<?php
  if(strlen($reports['fields_in_listing'])>0)
  {                  
    $fields_query = db_query("select * from app_fields where entities_id='" . $reports['entities_id'] . "' and id in (" . $reports['fields_in_listing']  . ") order by field(id," . $reports['fields_in_listing'] . ")");
    while($field = db_fetch_array($fields_query))
    {
      //check field access
      if(isset($fields_access_schema[$field['id']]))
      {
        if($fields_access_schema[$field['id']]=='hide') continue;
      }
      
      echo '<td class="gdfCell"></td>';
    }
  } 
?> 
    
  </tr>
  --></div>

  <div class="__template__" type="TASKBAR"><!--
  <div class="taskBox taskBoxDiv" taskId="(#=obj.id#)" >
    <div class="layout (#=obj.hasExternalDep?'extDep':''#)">
      <div class="taskStatus" status="(#=obj.status#)"></div>
      <div class="taskProgress" style="width:(#=obj.progress>100?100:obj.progress#)%; background-color:(#=obj.progress>100?'red':'rgb(153,255,51);'#);"></div>
      <div class="milestone (#=obj.startIsMilestone?'active':''#)" ></div>

      <div class="taskLabel"></div>
      <div class="milestone end (#=obj.endIsMilestone?'active':''#)" ></div>
    </div>
  </div>
  --></div>
  
  

</div>