<?php

class Zend_View_Helper_Pie extends Zend_View_Helper_Abstract {

  public function pie() {
    ?>
    <div class="pie-control">
    <?php
    $this->view->headScript()->appendFile('http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js');
    $this->view->headScript()->appendFile('http://ui.jquery.com/ui/ui.core.js');
    $this->view->headScript()->appendFile('http://ui.jquery.com/ui/ui.slider.js');
    $this->view->headScript()->appendFile($this->view->baseUrl().'/js/charitypie.js');
    ?>

    <?php
    if ($this->view->slices_count == 0) {
      ?>
      <p>
          This pie has no slices.
      </p>
      <?php
      if ($this->view->is_pie_owner) {
        ?>
        <p>
         To add a slice browse the charities and causes,
          and use the "add to my pie" link.
        </p>
        <?php
      }
    } else {
    ?>
    <!--<p>This pie has <?php print $this->view->slices_count ?>/10 slices</p>-->
      <?php
      if ($this->view->slice_changes) {
        ?><div class="notice"><?php
        if ($this->view->user()) {
        ?><p>Your latest changes have not been saved.
          <a href="<?php print $this->view->url(array('itemId'=>$this->view->pieId,'action'=>'save'),'pie') ?>" title="Save changes to this pie">Save</a> or
          <a href="<?php print $this->view->url(array('action'=>'reload','itemId'=>$this->view->pieId),'pie') ?>" title="Reload your saved pie">Undo</a>.</p><?php
        } else {
          ?>You need to <a href="<?php print $this->view->url(array('controller'=>'user','action'=>'register'),'default') ?>">register</a> to save your pie<?php
        }
        ?></div><?php
      }
      ?>

      <script type="text/javascript" src="<?php print $this->view->baseUrl() ?>/flash/version-2/js/swfobject.js"></script>
      <script type="text/javascript">
      var data;
      function open_flash_chart_data() {
        return JSON.stringify(data);
      }

      $(function(){
        $.getJSON('<?php print $this->view->url(array('itemId'=>$this->view->pieId,'action'=>'load'),'pie') ?>',null,
          function(returned){
            data = returned;
          })
        });

        swfobject.embedSWF(
          "<?php print $this->view->baseUrl() ?>/flash/version-2/open-flash-chart.swf", "my_chart", "550", "200",
          "9.0.0", "expressInstall.swf",
          {"data-file":"<?php print $this->view->url(array('itemId'=>$this->view->pieId,'action'=>'load'),'pie') ?>"}
        );
      </script>
      <div id="my_chart"></div>

      <?php
        if (empty($this->view->pie_slices)) {
          print "no slices";
        } else {
          ?>
          <div class="slices-control">
          <form action="<?php print $this->view->url(array('itemId'=>$this->view->pieId,'action'=>'update'),'pie'); ?>" method="POST" id="pie_slices"><?php
          foreach ($this->view->pie_slices as $slice) {
            if ($this->view->is_pie_owner) {
            ?>
            <div class="slice">
              <label><?php print $slice['recipient_name'] ?></label>
              <input type="text" value="<?php print $slice['size']; ?>" name="slice-<?php print $slice['slice_id']; ?>" id="slice-<?php print $slice['slice_id']; ?>" class="slice-size"/>
              <p><a href="<?php print $this->view->url(array('action'=>'deleteslice','itemId'=>$slice['slice_id']),'pie',true) ?>">remove from pie</a>
            </div>
            <?php
            } else {
              print $slice['recipient_name']."<br/>";
            }
          }
          if ($this->view->is_pie_owner) { ?><input type="submit" value="save changes"></form>
          </div><?php }
        }
    }
    ?>
    </div>
    <?php
  }

}