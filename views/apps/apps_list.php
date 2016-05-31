<!--Apps list  -->
<div class="row clearfix widget">
    <?php foreach ($modules as $k => $v): ?>
        <div class="col-md-3 col-sm-6 widget-entry">
            <div class="widget-entry-in">
                <div class="widget-entry-header">
                    <a href="<?php echo Ut::uri('app/id/' . $v->id) ?>" class="widget-entry-img"><img src="<?php echo Ut::getImageOrPlaceholder('modules/' . $v->icon) ?>" alt="<?php echo $v->title ?>"></a>
                    <h3><a href="<?php echo Ut::uri('app/id/' . $v->id) ?>"><?php echo Ut::cutText($v->title, 20) ?></a></h3>
                </div>
                <div class="widget-entry-footer">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fa widget-rating <?php echo $i > $v->rating ? 'fa-star-o' : 'fa-star israted' ?>"></i>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>