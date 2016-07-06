<!--Apps list  -->
<div class="app-row clearfix">
    <?php foreach ($modules as $k => $v): ?>
        <div class="widget-entry">
            <div class="widget-entry-in">
               <div class="widget-header">
                   <span class="widget-img">
                    <img class="widget-preview-img" src="<?php echo Ut::getImageOrPlaceholder('modules/' . $v->icon) ?>" alt="<?php echo $v->title ?>">
                    </span>
                    <h3><?php echo Ut::cutText($v->title, 20) ?></h3>
                </div>
                <div class="widget-footer">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fa widget-rating <?php echo $i > $v->rating ? 'fa-star-o' : 'fa-star israted' ?>"></i>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>