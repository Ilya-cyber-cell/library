<!DOCTYPE html>
  <html>
  <head>
    <title><?= $title; ?></title>
    <link rel="stylesheet" href="/reports.css">
  </head>
  <body>
    <div>
        <h1></h1>
    </div>
    <div class="main-table">
        <table class="reportTable">
            <tr class="reportTable"><th class="reportTable">Номер</th><th class="reportTable">Кем Выдана</th><th class="reportTable">Кому Выдана</th><th class="reportTable">Название</th><th class="reportTable">Дата</th><th class="reportTable">Состаяние</th></tr>
            <?php foreach ($tableContent as $item): ?>
                <tr class="reportTable">
                    <td class="reportTable"><?=$item['inventoryId'];?></td>
                    <td class="reportTable"><?=$item['fio'];?></td>
                    <td class="reportTable"><?=$item['localtion_fio'];?></td>
                    <td class="reportTable"><?=$item['Title'];?></td>
                    <td class="reportTable"><?=$item['date'];?></td>
                    <td class="reportTable"><?=$item['sateru'];?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <div class="main-footer">
    </div>
  </body>
</html>
