<!-- App ID -->
<div class="form form-inline form-page">
<div class="fieldset clearfix widget-detail">
    <div class="widget-detail-img">
        <img src="<?php echo Ut::getImageOrPlaceholder('modules/'.$module->icon) ?>" alt="<?php echo $module->title ?>">
    </div>
    <div class="widget-detail-body">
        <?php //var_dump($module) ?>
        <h1><?php echo $module->title ?></h1>
        <div class="widget-detail-list"><?php echo $module->description ?></div>
        <div class="widget-detail-list"><strong>Author:</strong> <?php echo $module->author ?></div>
        <?php if(!empty($module->homepage)): ?>
        <div class="widget-detail-list">
            <strong>Homepage:</strong> <a href="<?php echo $module->homepage ?>"><?php echo $module->homepage ?></a>
        </div>
         <?php endif; ?>
        <div class="widget-detail-list"><strong>Version:</strong> <?php echo $module->version ?></div>
         <div class="widget-detail-list"><strong>Installed:</strong> <?php echo $module->installed ?>x</div>
         <div class="widget-detail-list"><strong>Updated:</strong> <?php echo $module->last_updated ?></div>
          <div class="widget-detail-list">
              <strong>Rating:</strong>
              <?php for ($i = 1;$i <= 5;$i++): ?>
                    <i class="fa widget-rating <?php echo $i > $module->rating ? 'fa-star-o' : 'fa-star israted' ?>"></i>
               <?php endfor; ?>
                     (<?php echo $module->ratingscnt ?> <i class="fa fa-user"></i>)
          </div>
    </div>
</div>
 <div class="fieldset submit-entry">
        <a href="<?php echo Ut::uri('apps')?>" class="btn btn-default" title="Cancel">
            <i class="fa fa-reply"></i> <span class="btn-name">All Apps</span>
        </a>
        <a class="btn btn-submit" href="modules/<?php echo $module->file ?>" title="Download">
            <i class="fa fa-download"></i> <span class="btn-name">Download</span>
        </a>
    </div>
</div>