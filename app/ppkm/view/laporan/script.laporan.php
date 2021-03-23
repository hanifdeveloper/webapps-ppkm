const showMessage = function (status, message) {
    $.bootstrapGrowl("<h4><strong>" + message.title + "</strong></h4> <p>" + message.text + "</p>", {
        type: status,
        delay: 3000,
        allow_dismiss: true,
        offset: {
            from: "top",
            amount: 20
        }
    });
}

// Laporan Script
if ($("#laporan").length > 0) {
    const laporan = new frameduz("#laporan");
    const dialog = laporan.createDialog("#modal-fade");
    laporan.showTable = function () {
        laporan.loadTable({
            url: "/ppkm/laporan/list",
            data: $(".form-table").serialize(),
            onShow: function (content) {
                content.find("[data-toggle='tooltip']").tooltip();
                content.find(".btn-edit").each(function (index, element) {
                    $(element).on("click", function (e) {
                        e.preventDefault();
                        laporan.showForm(this.id);
                    });
                });

                content.find(".btn-delete").each(function (index, element) {
                    $(element).on("click", function (e) {
                        e.preventDefault();
                        const params = {
                            id: this.id,
                            message: this.getAttribute("data-message")
                        };
                        if (confirm(params.message)) {
                            const progress = laporan.createProgress(laporan.table.content);
                            setTimeout(function () {
                                app.sendData({
                                    url: "/ppkm/laporan/delete",
                                    data: params,
                                    token: "<?= $this->token; ?>",
                                    onSuccess: function (response) {
                                        // console.log(response);
                                        progress.remove();
                                        laporan.showTable();
                                        showMessage("info", response.message);
                                    },
                                    onError: function (error) {
                                        // console.log(error);
                                        progress.remove();
                                        showMessage("danger", error.message);
                                    }
                                });
                            }, 1000);
                        }
                    });
                });
            },
            onPage: function (page_number) {
                //console.log(page_number);
                $(".form-table").find("#page").val(page_number);
                laporan.showTable();
            }
        });
        // console.clear();
        return false;
    }

    laporan.showForm = function (id) {
        const form_content = laporan.form.clone();
        form_content.attr("id", "form-input");
        // Save Data
        form_content.on("submit", function (event) {
            if (form_content.find("#kecamatan_id").val() == "") {
                return alert("Pilih Kecamatan !");
            }

            if (form_content.find("#keldesa_id").val() == "") {
                return alert("Pilih Kel/Desa !");
            }

            const progress = laporan.createProgress(dialog.content);
            setTimeout(function () {
                app.sendData({
                    url: "/ppkm/laporan/save",
                    data: form_content.serialize(),
                    token: "<?= $this->token; ?>",
                    onSuccess: function (response) {
                        //console.log(response);
                        progress.remove();
                        dialog.modal("hide");
                        laporan.showTable();
                        showMessage("info", response.message);
                    },
                    onError: function (error) {
                        console.log(error);
                        progress.remove();
                        showMessage("danger", error.message);
                    }
                });
            }, 500);
        });

        // Load Form
        setTimeout(function () {
            app.sendData({
                url: "/ppkm/laporan/form",
                data: {
                    id
                },
                token: "<?= $this->token; ?>",
                onSuccess: function (response) {
                    //console.log(response);
                    const data = response.data;
                    const form = data.form;
                    laporan.form.createObject(form_content, {
                        id_laporan_covid: app.createForm.inputKey("id_laporan_covid", form.id_laporan_covid),
                        nama_warga: app.createForm.inputText("nama_warga", form.nama_warga).attr("required", true),
                        jenis_kelamin: app.createForm.selectOption("jenis_kelamin", data.pilihan_jenis_kelamin, form.jenis_kelamin).attr("required", true).addClass("select-select2"),
                        tempat_lahir: app.createForm.inputText("tempat_lahir", form.tempat_lahir).attr("required", true),
                        tanggal_lahir: app.createForm.inputText("tanggal_lahir", form.tanggal_lahir).attr("readonly", true).addClass("input-datepicker"),
                        kecamatan_id: app.createForm.selectOption("kecamatan_id", data.pilihan_kecamatan, form.kecamatan_id).attr("required", true).addClass("select-select2"),
                        keldesa_id: app.createForm.selectOption("keldesa_id", data.pilihan_keldesa, form.keldesa_id).attr("required", true).addClass("select-select2"),
                        rt: app.createForm.selectOption("rt", data.pilihan_rt, form.rt).attr("required", true).addClass("select-select2"),
                        rw: app.createForm.selectOption("rw", data.pilihan_rw, form.rw).attr("required", true).addClass("select-select2"),
                        tanggal_terkonfirmasi: app.createForm.inputText("tanggal_terkonfirmasi", form.tanggal_terkonfirmasi).attr("readonly", true).addClass("input-datepicker"),
                        status_kondisi: app.createForm.selectOption("status_kondisi", data.pilihan_status_kondisi, form.status_kondisi).attr("required", true).addClass("select-select2"),
                        kondisi_saat_ini: app.createForm.selectOption("kondisi_saat_ini", data.pilihan_kondisi_saat_ini, form.kondisi_saat_ini).attr("required", true).addClass("select-select2"),
                    });

                    form_content.find(".select-select2").css({
                        width: "100%"
                    }).select2({
                        dropdownParent: $("#modal-fade .modal-content")
                    });

                    form_content.find(".input-datepicker").css({
                        width: "100%"
                    }).datepicker({
                        format: "yyyy-mm-dd"
                    });

                    dialog.title.html(data.form_title);
                    dialog.body.html(form_content);
                    dialog.submit.attr("form", "form-input");
                    dialog.modal("show");

                },
                onError: function (error) {
                    console.log(error);
                }
            });
        }, 500);
    }

    laporan.modul.find(".filter-table").on("change", function (e) {
        e.preventDefault();
        $(".form-table").find("#page").val(1);
        laporan.showTable();
    });
    laporan.modul.find(".btn-form").on("click", function (e) {
        e.preventDefault();
        laporan.showForm(this.id);
    });
    laporan.modul.find(".btn-reload").on("click", function (e) {
        e.preventDefault();
        laporan.showTable();
    });

    laporan.showTable();
}