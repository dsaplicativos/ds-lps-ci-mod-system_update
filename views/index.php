<div class="container">
    <div class="card mb-2 mt-5 px-4 py-2">
        <h3 class="text-center mt-3"><i class="fa fa-cloud-upload mr-1"></i> Atualização do Sistema</h3><br>

        <div class="alert-<?= $info['color'] ?> text-center py-3 px-3">
            <?= $info['message'] ?>
        </div>

        <form action="<?php echo base_url('System_Update') ?>" method="post" enctype="multipart/form-data">
            <div class="card-block mt-3">
                <div class="file-field">
                    <div class="btn btn-primary btn-sm">
                        <span>Selecionar</span>
                        <input type="file" name="userfile">
                    </div>
                    <div class="file-path-wrapper">
                        <input class="file-path validate" type="text" name="file_dl" placeholder="(.xls, .xlsx)">
                    </div>
                </div>
                <div class="text-center mt-5 mb-4">
                    <button id="button" class="btn btn-primary">Enviar Arquivo</button>
                </div>
            </div>
        </form>
    </div>
</div>