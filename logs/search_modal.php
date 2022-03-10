<div class="modal" id="search-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- ヘッダー -->
            <div class="modal-header">
                <h5 class="modal-title">トレーニングログ検索</h5>
                <!-- 閉じるアイコン -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-bs-target="#search-modal">
                </button>
            </div>
            <!-- 本文 -->
            <div class="modal-body ">
                <!-- 検索フォーム -->
                <form method="get">
                    <div class="mb-2">
                        <div>
                            <label class="form-label for=" date">日付</label>
                            <input class="form-control" type="date" name="date" max="9999-12-31" id="date" value="<?= escape($date) ?>">
                        </div>
                        <div>
                            <label class="form-label for=" part">部位</label>
                            <select class="form-select" name="part" id="part">
                                <option value="">--</option>
                                <?php foreach ($form_parts as $form_part) : ?>
                                    <!-- 検索で入力された値とする場合、selected属性を付加する -->
                                    <option <?= $form_part === $part ? 'selected' : '' ?>><?= escape($form_part) ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label for=" machine">マシン</label>
                        <input class="form-control" type="text" name="machine" value="<?= escape($machine) ?>">
                    </div>
                    <div class="d-grid gap-3">
                        <button class="btn btn-warning" type="submit" data-bs-target="#confirm-modal">検索</button>
                        <a class="btn btn-secondary" href="index.php">クリア</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
