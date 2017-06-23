<?php
$format = Yii::$app->formatter;
if (!$debug) {
    $this->off(\yii\web\View::EVENT_END_BODY, [\yii\debug\Module::getInstance(), 'renderToolbar']);
}
?>
<table class="table table-bordered table-hover table-condensed" cellspacing="0" cellpadding="0">
    <thead class="thead-inverse">
    <tr>
        <th> Дата </th>
        <th> Сайт </th>
        <th colspan="4"> Desktop </th>
        <th colspan="4"> Tablet </th>
        <th colspan="4"> Mobile </th>
        <th colspan="4"> Total </th>

    </tr>
    </thead>
    <thead class="thead-default">
    <tr class="active">
        <th>  </th>
        <th>  </th>

        <th> Users</th>
        <th> Page Views</th>
        <th> Sessions</th>
        <th> Bounce Rate</th>

        <th> Users</th>
        <th> Page Views</th>
        <th> Sessions</th>
        <th> Bounce Rate</th>

        <th> Users</th>
        <th> Page Views</th>
        <th> Sessions</th>
        <th> Bounce Rate</th>

        <th> Users</th>
        <th> Page Views</th>
        <th> Sessions</th>


    </tr>
    </thead>
    <tbody>
    <?php
    $sumFunction = function($key, $users, $pageviews, $sessions, $bounces) use (&$sumList) {
        $sumList[$key]['users']+=$users;
        $sumList[$key]['pageviews']+=$pageviews;
        $sumList[$key]['sessions']+=$sessions;
        $sumList[$key]['bounces']+=$bounces;
    };


    $sumRowFunction = function($users, $pageviews, $sessions, $bounces) use (&$sumRowList) {
        $sumRowList['users']+=$users;
        $sumRowList['pageviews']+=$pageviews;
        $sumRowList['sessions']+=$sessions;
        $sumRowList['bounces']+=$bounces;
    };

    $subList = [
        'users' => 0,
        'pageviews' => 0,
        'sessions'  => 0,
        'bounces' => 0,
        'total' => 0,
    ];

    foreach ($statisticList as $data) {
        //$data = $data->attributes;
        ?>
        <tr>
            <td scope="row" rowspan="<?=count($data['statistic']) + 1;?>"> <?=$data['monthStart']?> </td>
        </tr>
        <?php
        $sumList['desktop'] = $subList;
        $sumList['tablet'] = $subList;
        $sumList['mobile'] = $subList;
        $sumList['total'] = $subList;

        foreach ($data['statistic'] as $item): ?>
            <?php $sumRowList = $subList;  ?>
            <tr>
                <td><?=str_replace('http://', '', $item['name']);?> </td>

                <?php
                $key  = 'desktop';
                list($users, $pageviews, $sessions, $bounces) = array_values($item['data'][$key]);?>
                <td> <?=$format->asInteger($users);?></td>
                <td> <?=$format->asInteger($pageviews);?></td>
                <td> <?=$format->asInteger($sessions);?></td>
                <td> <?=$format->asInteger($bounces);?>%</td>
                <?php  $sumFunction($key, $users, $pageviews, $sessions, $bounces); ?>
                <?php  $sumRowFunction($users, $pageviews, $sessions, $bounces); ?>


                <?php
                $key  = 'tablet';
                list($users, $pageviews, $sessions, $bounces) = array_values($item['data'][$key]);?>
                <td> <?=$format->asInteger($users);?></td>
                <td> <?=$format->asInteger($pageviews);?></td>
                <td> <?=$format->asInteger($sessions);?></td>
                <td> <?=$format->asInteger($bounces);?>%</td>
                <?php  $sumFunction($key, $users, $pageviews, $sessions, $bounces); ?>
                <?php  $sumRowFunction($users, $pageviews, $sessions, $bounces); ?>


                <?php
                $key  = 'mobile';
                list($users, $pageviews, $sessions, $bounces) = array_values($item['data'][$key]);?>
                <td> <?=$format->asInteger($users);?></td>
                <td> <?=$format->asInteger($pageviews);?></td>
                <td> <?=$format->asInteger($sessions);?></td>
                <td> <?=$format->asInteger($bounces);?>%</td>
                <?php  $sumFunction($key, $users, $pageviews, $sessions, $bounces); ?>
                <?php  $sumRowFunction($users, $pageviews, $sessions, $bounces); ?>


                <?php
                $key  = 'total';
                list($users, $pageviews, $sessions, $bounces) = array_values($sumRowList) ;?>
                <td> <?=$format->asInteger($users);?></td>
                <td> <?=$format->asInteger($pageviews);?></td>
                <td colspan="2"> <?=$format->asInteger($sessions);?></td>
                <?php  $sumFunction($key, $users, $pageviews, $sessions, $bounces); ?>
               </tr>

        <?php endforeach; ?>
            <tr class="warning">
                <td colspan="2"> Сумма:</td>

                <td> <?=$format->asInteger($sumList['desktop']['users']);?> </td>
                <td> <?=$format->asInteger($sumList['desktop']['pageviews']);?> </td>
                <td> <?=$format->asInteger($sumList['desktop']['sessions']);?> </td>
                <td> </td>

                <td> <?=$format->asInteger($sumList['tablet']['users']);?> </td>
                <td> <?=$format->asInteger($sumList['tablet']['pageviews']);?> </td>
                <td> <?=$format->asInteger($sumList['tablet']['sessions']);?> </td>
                <td>  </td>

                <td> <?=$format->asInteger($sumList['mobile']['users']);?> </td>
                <td> <?=$format->asInteger($sumList['mobile']['pageviews']);?> </td>
                <td> <?=$format->asInteger($sumList['mobile']['sessions']);?> </td>
                <td>  </td>

                <td> <?=$format->asInteger($sumList['total']['users']);?> </td>
                <td> <?=$format->asInteger($sumList['total']['pageviews']);?> </td>
                <td> <?=$format->asInteger($sumList['total']['sessions']);?> </td>

            </tr>
    <?php } ?>

    </tbody>
</table>
