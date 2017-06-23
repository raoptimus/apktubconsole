<?php
use yii\jui\DatePicker;
$format = Yii::$app->formatter;
?>

<form  method="get" class="well well-sm form-inline">
  <div class="form-group field-form-search">
      Выбрать диапазон:
      <?=DatePicker::widget([
          'name'  => 'monthStart',
          'value'  => $monthStart,
          'language' => 'ru',
          'dateFormat' => 'yyyy-MM-dd',
      ]);
      ?>
    по
      <?=DatePicker::widget([
          'name'  => 'monthEnd',
          'value'  => $monthEnd,
          'language' => 'ru',
          'dateFormat' => 'yyyy-MM-dd',
      ]);
      ?>

    <input type="submit" class="btn-primary btn">
    <input type="submit" class="btn btn-info" name="xls"  value="excel">
    <input type="submit" class="btn btn" name="csv"  value="csv">
  </div>
</form>
<div class="container-fluid">
    <div class="row">
        <div class="panel panel-default pull-right" style="width:100%">
            <div class="panel-heading">
                Google analytics
            </div>
            <?php echo $this->render('_list', [
                'statisticList' => $statisticList,
                'monthStart' => $monthStart,
                'debug' => $debug,

            ]); ?>

        </div>
    </div>
</div>