<body>
    <div id="page-wrapper" class="page-loading">
        <?php $this->getView('ppkm', 'main', 'preloader', ''); ?>
        <div id="page-container" class="header-fixed-top sidebar-visible-lg-full">
            <!-- Main Sidebar -->
            <?php $this->getView('ppkm', 'main', 'navbar', ''); ?>
            <!-- END Main Sidebar -->

            <!-- Main Container -->
            <div id="main-container">
                <!-- Header -->
                <?php $this->getView('ppkm', 'main', 'header', ''); ?>
                <!-- END Header -->

                <!-- Modal -->
                <?php $this->getView('ppkm', 'main', 'modal', ''); ?>
                <!-- END Modal -->

                <!-- Page content --> 
                <div id="page-content">
                    <!-- Blank Header -->
                    <div class="content-header">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="header-section">
                                    <h1><?= $page_title ?></h1>
                                </div>
                            </div>
                            <div class="col-sm-6 hidden-xs">
                                <div class="header-section">
                                    <ul class="breadcrumb breadcrumb-top">
                                        <?= $breadcrumb ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- END Blank Header -->

                    <!-- Get Started Block -->
                    <div id="laporan" class="block full">
                        <div class="block-title">
                            <h2>Total Warga : <span class="total-data">0</span> Data</h3>
                            <div class="block-options pull-left">
                                <button class="btn btn-effect-ripple btn-default btn-reload" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Reload"><i class="fa fa-refresh"></i></button>
                            </div>
                            <div class="block-options pull-right">
                                <button id="" class="btn btn-effect-ripple btn-primary btn-form" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Tambah Data"><i class="fa fa-plus"></i> Tambah Data</button>
                            </div>
                        </div>
                        <form class="form-table form-horizontal form-bordered" onsubmit="return false;" autocomplete="off">
                            <?= comp\BOOTSTRAP::inputKey('page', '1') ?>
                            <?= comp\BOOTSTRAP::inputKey('size', '10') ?>
                            <div class="form-group form-actions">
                                <div class="col-sm-4">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-search"></i>
                                        </span>
                                        <?= comp\BOOTSTRAP::inputText('cari', 'text', '', 'class="form-control filter-table" placeholder="Cari Nama ..."') ?>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <?= comp\BOOTSTRAP::inputSelect('status', $pilihan_status_kondisi, '', 'class="form-control filter-table select-select2" placeholder="Pilih Status ..."') ?>
                                </div>
                                <div class="col-sm-4">
                                    <?= comp\BOOTSTRAP::inputSelect('kondisi', $pilihan_kondisi_saat_ini, '', 'class="form-control filter-table select-select2" placeholder="Pilih Kondisi ..."') ?>
                                </div>
                            </div>
                            <div class="form-group form-actions">
                                <div class="col-sm-3">
                                    <?= comp\BOOTSTRAP::inputSelect('kecamatan', $pilihan_kecamatan, '', 'class="form-control filter-table select-select2" placeholder="Pilih Kondisi ..."') ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= comp\BOOTSTRAP::inputSelect('keldesa', $pilihan_keldesa, '', 'class="form-control filter-table select-select2" placeholder="Pilih Kondisi ..."') ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= comp\BOOTSTRAP::inputSelect('rt', $pilihan_rt, '', 'class="form-control filter-table select-select2" placeholder="Pilih Kondisi ..."') ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= comp\BOOTSTRAP::inputSelect('rw', $pilihan_rw, '', 'class="form-control filter-table select-select2" placeholder="Pilih Kondisi ..."') ?>
                                </div>
                            </div>
                        </form>
                        <div class="fztable-content">
                            <div class="table-responsive">
                                <table class="table table-borderless table-condensed table-hover">
                                    <thead class="table-primary">
                                        <th>#</th>
                                        <th>Nama</th>
                                        <th>Domisili</th>
                                        <th>Status</th>
                                        <th>Kondisi</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{number}</td>
                                            <td><strong>{nama_warga}</strong></td>
                                            <td>
                                                Kec. <strong>{nama_kecamatan}</strong><br>
                                                Kel/Desa. <strong>{nama_keldesa}</strong><br>
                                                <strong>{nama_rt}, {nama_rw}</strong>
                                            </td>
                                            <td><strong>{nama_status_kondisi}</strong></td>
                                            <td><strong>{nama_kondisi_saat_ini}</strong></td>
                                            <td>
                                                <button data-toggle="tooltip" title="Edit Data" id="{id_laporan_covid}" class="btn btn-effect-ripple btn-sm btn-success btn-edit"><i class="fa fa-pencil"></i></button>
                                                <button data-toggle="tooltip" title="Delete Data" id="{id_laporan_covid}" class="btn btn-effect-ripple btn-sm btn-danger btn-delete" data-message="Yakin data warga a/n {nama_warga} akan dihapus ?"><i class="fa fa-times"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="fztable-paging" style="border-top:2px solid #ccc;">
                                <ul class="pagination pagination-sm">
                                    <li><a href="javascript:void(0)" page-number="">{page}</a></li>
                                </ul>
                            </div>
                        </div>
                        <form action="page_forms_components.html" method="post" class="fzform-content form-bordered" onsubmit="return false;" autocomplete="off">
                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                    <label for="nama_warga">Nama Warga</label>
                                        <span data-form-object="id_laporan_covid"></span>
                                        <span data-form-object="nama_warga"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="jenis_kelamin">Jenis Kelamin</label>
                                        <span data-form-object="jenis_kelamin"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="tempat_lahir">Tempat Lahir</label>
                                        <span data-form-object="tempat_lahir"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="tanggal_lahir">Tanggal Lahir</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <span data-form-object="tanggal_lahir"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="kecamatan_id">Kecamatan</label>
                                        <span data-form-object="kecamatan_id"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="keldesa_id">Kel/Desa</label>
                                        <span data-form-object="keldesa_id"></span>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label for="rt">RT</label>
                                        <span data-form-object="rt"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="rw">RW</label>
                                        <span data-form-object="rw"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="tanggal_terkonfirmasi">Tanggal Terkonfirmasi Covid 19</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                            <span data-form-object="tanggal_terkonfirmasi"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="status_kondisi">Status Kondisi</label>
                                        <span data-form-object="status_kondisi"></span>
                                    </div>
                                    <div class="form-group">
                                        <label for="kondisi_saat_ini">Kondisi Saat Ini</label>
                                        <span data-form-object="kondisi_saat_ini"></span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- END Get Started Block -->
                </div>
                <!-- END Page Content -->
            </div>
            <!-- END Main Container -->
        </div>
        <!-- END Page Container -->
    </div>
    <!-- END Page Wrapper -->

    <!-- jQuery, Bootstrap, jQuery plugins and Custom JS code -->
    <?= $jsPath; ?>
    <script src="<?= $api_path.'/script'; ?>"></script>
    <script src="<?= $url_path.'/script'; ?>"></script>
</body>