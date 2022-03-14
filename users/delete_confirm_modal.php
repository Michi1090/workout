<div class="modal" id="confirm-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- ヘッダー -->
            <div class="modal-header">
                <h5 class="modal-title">確認</h5>
                <!-- 閉じるアイコン -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-bs-target="#confirm-modal">
                </button>
            </div>
            <!-- 本文 -->
            <div class="modal-body ">
                <p>ユーザー登録を削除すると、すべての筋トレログが削除され元に戻せません。</p>
                <p>本当にユーザー登録を削除しますか？</p>
            </div>
            <!-- フッター -->
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="row my-2">
                        <button class="btn btn-danger col me-2" type="button" onclick="return submit();">削除</button>
                        <button class="btn btn-secondary col ms-2" data-bs-dismiss="modal" data-bs-target="#confirm-modal" type="button">閉じる</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
