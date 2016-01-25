<!-- flash view -->
<div class="alert alert-<?php echo $flash['status'] ?>">
    <?php foreach ($flash['message'] as $value): ?>
    <?php echo $value ?><br />  
    <?php endforeach; ?>
</div>