
/* Write here your custom javascript codes */

var Datepicker = function () {

    return {
        //Datepickers
        initDatepicker: function () {
            // Regular datepicker
            $('#txtDate').datepicker({
                dateFormat: 'dd-mm-yy',
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>'
            });
            $('#txtDateImei').datepicker({
                dateFormat: 'dd-mm-yy',
                prevText: '<i class="fa fa-angle-left"></i>',
                nextText: '<i class="fa fa-angle-right"></i>'
            });
        }

    };
}();


function loadMoreBerita(a) {

}

function clearsearch() {
    $('#divResultSearch').html('Silahkan lakukan pencarian form di atas. Isikan parameter pencarian dan klik tombol Submit ! ...            <p>               <i style="color: blue;">Please input parameters and then click the Search button! ..</i> </p>');
    $('#txtValSearch').val('');
}



$('#submitKurs').click(function (e) {
    var d = $('#date').val();
    $.ajax({
        type: 'post',
        url: 'kurs.html',
        dataType: 'text',
        data: {
            tglKurs: d,
            content: 'browseKurs'
        },
        success: function (response) {
            $("#resultKurs").html(response);

        },
        error: function () {
            $("#resultKurs").html("Not Found !!<br> Pencarian tidak ditemukan");
        }
    });
});

$('#clearKurs').click(function (e) {
    $("#date").val('');
    $("#resultKurs").html('');
});

$('#btnEmailRecovery').click(function (e) {
    if (!validateEmail($('#txtEmailPengadu').val())) {
        alert('Format email tidak baku !');
        reloadCaptcha();
    } else if ($('#txtCaptcha').val() === '') {
        alert('Key code belum diisi ! ');
        reloadCaptcha();
    } else {
        $('#btnEmailRecovery').addClass('disabled');
        $('#btnEmailRecovery').html('Loading ...');
        $.ajax({
            url: 'pengaduan.html',
            type: 'post',
            dataType: 'text',
            data: {
                content: 'recoveryEmail',
                txtCaptcha: $('#txtCaptcha').val(),
                txtEmailRecovery: $('#txtEmailPengadu').val()
            },
            success: function (response) {
                if (response === 'success') {
                    alert('Harap cek email anda untuk melihat nomor tiket pengaduan !');
                } else if (response === 'no_data') {
                    alert('Belum ada Pengaduan dengan email pengadu ' + $('#txtEmailPengadu').val());
                } else {
                    alert('Gagal kirim email recovery tiket pengaduan ! :' + response);
                }
                $('#btnEmailRecovery').removeClass('disabled');
                $('#btnEmailRecovery').html('Submit');
                $('#txtEmailPengadu').val('');
                reloadCaptcha();
            }
        });
    }
});


$('#kirimPengaduan').click(function (e) {
    if ($('#txtNamaPengadu').val() === '') {
        alert('Nama Pengadu belum diisi !');
    } else if ($('#txtNamaPengadu').val().length < parseInt('3') || $('#txtNamaPengadu').val().length > parseInt('30')) {
        alert('Nama Pengadu minimal 3 dan maksimal 30 huruf !');
    } else if ($('#txtAlamatPengadu').val() === '') {
        alert('Alamat Pengadu belum diisi ! ');
    } else if (!validateEmail($('#txtEmailPengadu').val())) {
        alert('Format email tidak baku !');
    } else if ($('#txtKepadaPengaduan').val() === '') {
        alert('Tujuan Pengaduan belum diisi ! ');
    } else if ($('#txtJudulPengaduan').val() === '') {
        alert('Judul Pengaduan belum diisi ! ');
    } else if ($('#txtJudulPengaduan').val().length > parseInt('100')) {
        alert('Judul pengaduan melebihi batas 100 huruf/karakter !');
    } else if ($('#txtLokasiPengaduan').val() === '') {
        alert('Lokasi Kejadian belum di pilih !');
    } else if ($('#txtKotaPengaduan').val() === '') {
        alert('Kota Kejadian belum di pilih');
    } else if ($('#txtWaktuKejadianUraian').val() === '' && $('.txtTgl').val() === '') {
        alert('Harap inputkan Waktu Kejadian, dapat berupa uraian waktu maupun tanggal kejadian !');
    } else if ($('#terlapor').val() === '') {
        alert('Data pegawai yang di laporkan belum diisi !');
    } else if ($('#uraian').val() === '') {
        alert('Uraian pengaduan belum diisi !');
    } else if ($('#uraian').val().length < parseInt('200')) {
        alert('Uraian pengaduan terlalu pendek, minimal 200 huruf/karakter');
    } else if ($('#txtCaptcha').val() === '') {
        alert('Key code belum diisi ! ');
    } else {
        $('#kirimPengaduan').addClass('disabled');
        $('#kirimPengaduan').html('Loading ...');
        $.ajax({
            url: 'pengaduan.html',
            type: 'post',
            dataType: 'text',
            data: {
                content: 'sendPengaduan',
                txtNamaPengadu: $('#txtNamaPengadu').val(),
                txtAlamatPengadu: $('#txtAlamatPengadu').val(),
                txtEmailPengadu: $('#txtEmailPengadu').val(),
                txtTeleponPengadu: $('#txtTeleponPengadu').val(),
                txtJudulPengaduan: $('#txtJudulPengaduan').val(),
                txtKepadaPengaduan: $('#txtKepadaPengaduan').val(),
                txtLokasiPengaduan: $('#txtLokasiPengaduan').val(),
                txtKotaPengaduan: $('#txtKotaPengaduan').val(),
                txtWaktuKejadian: $('#txtTgl').val(),
                txtWaktuKejadianUraian: $('#txtWaktuKejadianUraian').val(),
                txtUraianPengaduan: $('#uraian').val(),
                txtTerlapor: $('#terlapor').val(),
                txtCaptcha: $('#txtCaptcha').val()
            },
            success: function (response) {
                var hasil = response.split("|");
                if (hasil[0] === 'success') {
                    alert('Pengaduan berhasil dikirim, silahkan gunakan nomor tiket untuk mengetahui proses tindak lanjutnya !');
                    $.ajax({
                        url: 'pengaduan.html',
                        type: 'post',
                        dataType: 'text',
                        data: {
                            content: 'recoveryEmailList',
                            txtEmailRecovery: $('#txtEmailPengadu').val()
                        }
                    });
                    $.ajax({
                        url: 'pengaduan.html',
                        type: 'post',
                        dataType: 'text',
                        data: {
                            content: 'loadTiketList',
                            txtNamaPengadu: $('#txtNamaPengadu').val(),
                            txtAlamatPengadu: $('#txtAlamatPengadu').val(),
                            txtEmailPengadu: $('#txtEmailPengadu').val(),
                            txtTeleponPengadu: $('#txtTeleponPengadu').val(),
                            txtJudulPengaduan: $('#txtJudulPengaduan').val()
                        }, success: function (response) {
                            $('#contentPengaduan').html(response);
                        }
                    });
                } else {
                    alert('Data pengaduan tidak dapat dikirim, karena ' + response);
                }
                $('#kirimPengaduan').removeClass('disabled');
                $('#kirimPengaduan').html('Kirim Data Pengaduan');
                reloadCaptcha();
            },
            error: function () {
                alert("Error, Rekam Pengaduan Failed");
            }

        });
    }
});

$('#kirimPengaduanAdd').click(function (e) {
    if ($('#txtKepadaPengaduanAdd').val() === '') {
        alert('Tujuan Pengaduan belum diisi ! ');
    } else if ($('#txtJudulPengaduanAdd').val() === '') {
        alert('Judul Pengaduan belum diisi ! ');
    } else if ($('#txtJudulPengaduanAdd').val().length > parseInt('100')) {
        alert('Judul pengaduan melebihi batas 100 huruf/karakter !');
    } else if ($('#txtLokasiPengaduanAdd').val() === '') {
        alert('Lokasi Kejadian belum di pilih !');
    } else if ($('#txtKotaPengaduanAdd').val() === '') {
        alert('Kota Kejadian belum di pilih');
    } else if ($('#txtWaktuKejadianUraianAdd').val() === '' && $('.txtTglAdd').val() === '') {
        alert('Harap inputkan Waktu Kejadian, dapat berupa uraian waktu maupun tanggal kejadian !');
    } else if ($('#terlaporAdd').val() === '') {
        alert('Data pegawai yang di laporkan belum diisi !');
    } else if ($('#uraianAdd').val() === '') {
        alert('Uraian pengaduan belum diisi !');
    } else if ($('#uraianAdd').val().length < parseInt('200')) {
        alert('Uraian pengaduan terlalu pendek, minimal 200 huruf/karakter');
    } else if ($('#txtCaptcha').val() === '') {
        alert('Key code belum diisi ! ');
    } else {
        $('#kirimPengaduanAdd').addClass('disabled');
        $('#kirimPengaduanAdd').html('Loading ...');
        $.ajax({
            url: 'pengaduan.html',
            type: 'post',
            dataType: 'text',
            data: {
                content: 'sendPengaduanAdd',
                txtIdPengadu: $('#txtIdPengaduAdd').val(),
                txtNamaPengadu: $('#txtNamaPengaduAdd').val(),
                txtAlamatPengadu: $('#txtAlamatPengaduAdd').val(),
                txtEmailPengadu: $('#txtEmailPengaduAdd').val(),
                txtTeleponPengadu: $('#txtTeleponPengaduAdd').val(),
                txtJudulPengaduan: $('#txtJudulPengaduanAdd').val(),
                txtKepadaPengaduan: $('#txtKepadaPengaduanAdd').val(),
                txtLokasiPengaduan: $('#txtLokasiPengaduanAdd').val(),
                txtKotaPengaduan: $('#txtKotaPengaduanAdd').val(),
                txtWaktuKejadian: $('#txtTglAdd').val(),
                txtWaktuKejadianUraian: $('#txtWaktuKejadianUraianAdd').val(),
                txtUraianPengaduan: $('#uraianAdd').val(),
                txtTerlapor: $('#terlaporAdd').val(),
                txtCaptcha: $('#txtCaptcha').val()
            },
            success: function (response) {
                var hasil = response.split("|");
                if (hasil[0] === 'success') {
                    alert('Pengaduan berhasil dikirim, silahkan gunakan nomor tiket untuk mengetahui proses tindak lanjutnya !');
                    $.ajax({
                        url: 'pengaduan.html',
                        type: 'post',
                        dataType: 'text',
                        data: {
                            content: 'recoveryEmailList',
                            txtEmailRecovery: $('#txtEmailPengaduAdd').val()
                        }
                    });
                    $.ajax({
                        url: 'pengaduan.html',
                        type: 'post',
                        dataType: 'text',
                        data: {
                            content: 'loadTiketList',
                            txtNamaPengadu: $('#txtNamaPengaduAdd').val(),
                            txtAlamatPengadu: $('#txtAlamatPengaduAdd').val(),
                            txtEmailPengadu: $('#txtEmailPengaduAdd').val(),
                            txtTeleponPengadu: $('#txtTeleponPengaduAdd').val(),
                            txtJudulPengaduan: $('#txtJudulPengaduanAdd').val()
                        }, success: function (response) {
                            $('#contentPengaduan').html(response);
                        }
                    });
                } else {
                    alert('Data pengaduan tidak dapat dikirim, karena ' + response);
                }
                $('#kirimPengaduanAdd').removeClass('disabled');
                $('#kirimPengaduanAdd').html('Kirim Data Pengaduan');
                reloadCaptcha();
            },
            error: function () {
                alert("Error, Rekam Pengaduan Failed");
            }

        });
    }
});

function onKlikProvinsi(val) {
    $.ajax({
        url: 'pengaduan.html',
        type: 'post',
        dataType: 'text',
        data: {
            content: 'loadKota',
            txtKdProvinsi: val
        },
        success: function (response) {
            $('#listkota').html(response);
            $('#listkota.mad-select-drop').html(response);
        }
    });
}

function onKlikProvinsiAdd(val) {
    $.ajax({
        url: 'pengaduan.html',
        type: 'post',
        dataType: 'text',
        data: {
            content: 'loadKota',
            txtKdProvinsi: val
        },
        success: function (response) {
            $('#listkotaAdd').html(response);
            $('#listkotaAdd.mad-select-drop').html(response);
        }
    });
}

$('#btnTrackingPengaduan').click(function (e) {
    if ($('#txtNomorTiket').val() === '' && $('#txtNomorTiket').val().length !== '14') {
        alert('Nomor tiket tidak sesuai !!!');
        $('#txtNomorTiket').focus();
        reloadCaptcha();
    } else if ($('#txtCaptcha').val() === '') {
        alert('Key code belum diisi !');
        reloadCaptcha();
    } else {
        $('#btnTrackingPengaduan').addClass('disabled');
        $('#btnTrackingPengaduan').html('Loading ...');
        $.ajax({
            url: 'pengaduan.html',
            type: 'post',
            dataType: 'text',
            data: {
                content: 'loadTiket',
                txtCaptcha: $('#txtCaptcha').val(),
                txtNomorTiket: $('#txtNomorTiket').val()
            },
            success: function (response) {
                $('#txtNomorTiket').val('');
                reloadCaptcha();
                $('#contentPengaduan').html(response);
            },
            error: function () {
                alert('Nomor tiket tidak ditemukan !!!');
            }
        });
    }
});


var openWindow = function (url, wName, w, h) {
    var LeftPosition = (screen.width) ? (screen.width - w) / 2 : 0;
    var TopPosition = (screen.height) ? (screen.height - h) / 2 : 0;
    var settings = 'height=' + h + ',width=' + w + ',top=' + TopPosition + ',left=' + LeftPosition + ',scrollbars=yes,resizable';
    var win = window.open(url, wName, settings);
    return win;
};

$('#moreNews').click(function (e) {
    var paramNum = $('#paramNum').val();
//    alert(paramNum);
    $("#resultBerita").append('<div id="resultBerita' + paramNum + '"></div>');

    $.ajax({
        url: 'berita.html',
        type: 'post',
        dataType: 'text',
        data: {
            start: paramNum,
            content: 'nextNews'
        },
        success: function (response) {
            $("#resultBerita" + paramNum).html(response);
            var nextNum = parseFloat(paramNum) + 1;
            $('#paramNum').val(nextNum);

        },
        error: function () {
            $("#resultBerita" + paramNum).html("Error Load !!<br> Load Berita Ke Server Gagal....");
        }
    });

//  
});

$('#submitHS').click(function () {
    if ($('#txtParHS').val() === '' || $('#txtValHS').val() === '') {
        alert('Parameter tidak lengkap !');
    } else {
        $.ajax({
            url: 'btki.html',
            type: 'post',
            dataType: 'text',
            data: {
                txtParHS: $('#txtParHS').val(),
                txtValHS: $('#txtValHS').val()
            },
            success: function (response) {
                $("#divResultHS").html(response);
            },
            error: function () {
                $("#divResultHS").html("Error Load !!<br> Load HS Ke Server Gagal....");
            }

        });
    }
//  
});

$('#clearHS').click(function () {
    $('#divResultHS').html('Silahkan lakukan pencarian form di atas. Isikan parameter pencarian dan klik tombol Submit ! ...            <p>               <i style="color: blue;">Please input parameters and then click the Search button! ..</i> </p>');
    $('#txtParHS').val('');
    $('#txtValHS').val('');
});

$('#clearBK').click(function () {
    $('#divResultBK').html('Silahkan lakukan pencarian form di atas. Isikan parameter pencarian dan klik tombol Submit ! ...            <p>               <i style="color: blue;">Please input parameters and then click the Search button! ..</i> </p>');
    $('#txtNpwpPjt').val('');
    $('#txtNoBarang').val('');
});

$('#clearBKB').click(function () {
    $('#divResultBKB').html('Silahkan lakukan pencarian form di atas. Isikan parameter pencarian dan klik tombol Submit ! ...            <p>               <i style="color: blue;">Please input parameters and then click the Search button! ..</i> </p>');
    $('#txtNpwpPjt').val('');
    $('#txtNoBarang').val('');
});

var reloadCaptcha = function () {
    $('#txtCaptcha').val("");
    var obj = $('#image');
    var src2 = obj.attr("src");
    var pos = src2.indexOf('?');
    if (pos >= 0) {
        src2 = src2.substr(0, pos);
    }
    var date = new Date();
    obj.attr("src", src2 + '?v=' + date.getTime());
    return false;
};

$('#submitBK').click(function () {
    if ($('#txtNoBarang').val() === '') {
        alert('Parameter tidak lengkap !');
    } else if ($('#txtCaptcha').val() === '') {
        alert('Key code belum diisi ! ');
    } else {
        $('#infoBK').html('Loading...');
        $('#infoBK').css("display", "block");
        $('#accordion').html('');
        $('#resultBKNew').css("display", "none");
        $.ajax({
            url: 'barangkiriman.html',
            type: 'post',
            dataType: 'text',
            data: {
                txtCaptcha: $('#txtCaptcha').val(),
                txtNoBarang: $('#txtNoBarang').val().toUpperCase()
            },
            success: function (response) {
                console.log(response);
                var res = response.split("||");
                console.log(res[0] + " - " + res[1]);
                if (res[0] === "wrongcaptcha") {
                    $('#resultBKNew').css("display", "none");
                    $('#infoBK').css("display", "block");
                    $("#infoBK").html("<div style=\"color:red\"> Pengisian key code tidak sesuai ! </div>");
                } else if (res[0] === "1") {
                    populateTrackingResult40(res[1]);
                } else if (res[0] === "0") {
                    populateTrackingResult(res[1]);
                } else {
                    $('#resultBKNew').css("display", "none");
                    $('#infoBK').css("display", "block");
                    $("#infoBK").html("<div style=\"color:red\"> " + res[1] + " </div>");
                }
                reloadCaptcha();
            },
            error: function () {
                // $("#divResultBK").html("Error Load !!<br> Load HS Ke Server Gagal....");
                $("#divResultBK").html("Error Load !! Ke Server Gagal....");
            }

        });
    }
//  
});

$('#submitBKB').click(function () {
    if ($('#txtNoBarang').val() === '') {
        alert('Parameter tidak lengkap !');
    } else if ($('#txtCaptcha').val() === '') {
        alert('Key code belum diisi ! ');
    } else {
        $('#infoBKB').html('Loading...');
        $('#infoBKB').css("display", "block");
        $('#accordion').html('');
        $('#resultBKBNew').css("display", "none");
        $.ajax({
            url: 'barangkirimanbatam.html',
            type: 'post',
            dataType: 'text',
            data: {
                txtCaptcha: $('#txtCaptcha').val(),
                txtNoBarang: $('#txtNoBarang').val().toUpperCase()
            },
            success: function (response) {
                populateTrackingResultBKB(response);

            },
            error: function () {
                // $("#divResultBK").html("Error Load !!<br> Load HS Ke Server Gagal....");
                $("#divResultBKB").html("Error Load !! Ke Server Gagal....");
            }

        });
    }
//  
});

$('#btnSubmitPertanyaan').click(function () {
    if ($('#txtNamaPenanya').val() === '' ||
            $('#txtEmailPenanya').val() === '' ||
            $('#txtNoTelpPenanya').val() === '' ||
            $('#txtPertanyaan').val() === '') {
        alert('Parameter tidak lengkap !');
    } else if ($('#txtCaptcha').val() === '') {
        alert('Key code belum diisi ! ');
    } else {
        $.ajax({
            url: 'tanyabravo.html',
            type: 'post',
            dataType: 'text',
            data: {
                txtCaptcha: $('#txtCaptcha').val(),
                txtNamaPenanya: $('#txtNamaPenanya').val(),
                txtEmailPenanya: $('#txtEmailPenanya').val(),
                txtNoTelpPenanya: $('#txtNoTelpPenanya').val(),
                txtPertanyaan: $('#txtPertanyaan').val()
            },
            success: function (response) {
                alert('Pertanyaan ' + response + ' dikirim.');
                $('#txtNamaPenanya').val('');
                $('#txtEmailPenanya').val('');
                $('#txtNoTelpPenanya').val('');
                $('#txtPertanyaan').val('');
                $('#txtCaptcha').val('');
                location.reload();
            },
            error: function () {
                alert("Gagal mengirimkan pertanyaan !");
            }

        });
    }
});

//qrcode
function generateQrCodeImei() {
    try {
        var qr = $('#qrcodeimei');
        var z = qr.getAttribute("kode");
        var qrcode = new QRCode(qr, {
            width: 200,
            height: 200
        });

        function makeCode() {
            qrcode.makeCode(z);
        }
        makeCode();
    } catch (e) {
    }
}

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function alphaNumericWithSpace(text) {
    var format = /^[ a-zA-Z0-9]+$/;
    return format.test(text);
}

function alphaNumeric(text) {
    var format = /^[a-zA-Z0-9]+$/;
    return format.test(text);
}

function passportFormat(text) {
    var format = /^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/;
    return format.test(text);
}

function letterOnly(text) {
    var format = /^[ a-zA-Z]+$/;
    return format.test(text);
}

function numberOnly(text) {
    var format = /^[0-9]+$/;
    return format.test(text);
}

function imageResizeCanvas() {
    preview_image();

    async function preview_image() {
        const file = document.getElementById('txtFileInput');
        const res = await image_to_base64(file.files[0]);
        if (res) {
            const old_size = calc_image_size(res);
            if (old_size > 2048) {
                const resized = await reduce_image_file_size(res);
                $('#txtFile').val(file.files[0].name + "#" + resized);
                const new_size = calc_image_size(resized);
//                $('#txtFileSize').html('old_size=> ' + old_size + 'KB' + " === " + 'new_size=> ' + new_size + 'KB');
//                console.log('new_size=> ', new_size, 'KB');
//                console.log('old_size=> ', old_size, 'KB');
                document.getElementById("new").src = resized;
                return resized;
            } else {
//                console.log('image already small enough');
                const new_size = calc_image_size(res);
//                console.log('new_size=> ', new_size, 'KB');
//                console.log('old_size=> ', old_size, 'KB');
                document.getElementById("new").src = res;
                return res;
            }
        } else {
            console.log('return err');
        }
    }

    async function reduce_image_file_size(base64Str, MAX_WIDTH = 1024, MAX_HEIGHT = 1024) {
        let resized_base64 = await new Promise((resolve) => {
            let img = new Image();
            img.src = base64Str;
            img.onload = () => {
                let canvas = document.createElement('canvas');
                let width = img.width;
                let height = img.height;

                if (width > height) {
                    // lanscape
                    if (width > MAX_WIDTH) {
                        height *= MAX_WIDTH / width;
                        width = MAX_WIDTH;
                    }
                } else {
                    //portrait
                    if (height > MAX_HEIGHT) {
                        width *= MAX_HEIGHT / height;
                        height = MAX_HEIGHT;
                    }
                }
                canvas.width = width;
                canvas.height = height;
                let ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);
                resolve(canvas.toDataURL()); // this will return base64 image results after resize
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            };
        });
        return resized_base64;
    }

    async function image_to_base64(file) {
        let result_base64 = await new Promise((resolve) => {
            let fileReader = new FileReader();
            fileReader.onload = (e) => resolve(fileReader.result);
            fileReader.onerror = (error) => {
                console.log(error);
                alert('An Error occurred please try again, File might be corrupt');
            };
            fileReader.readAsDataURL(file);
        });
        return result_base64;
    }

    function calc_image_size(image) {
        let y = 1;
        if (image.endsWith('==')) {
            y = 2;
        }
        const x_size = (image.length * (3 / 4)) - y;
        return Math.round(x_size / 1024);
    }
}

$("#imei11, #imei12").on("keyup blur", function () {
    let imei = $(this).val();
    if (imei.length >= 8) {
        $.ajax({
            url: 'register-imei.html',
            type: 'POST',
            dataType: 'text',
            data: {
                txtContent: 'cekImei',
                txtImei: imei
            },
            success: function (response) {
                var data = JSON.parse(response);
                if (data.status === true) {
                    let merkTipe = data.dataTac.merkTipe;
                    $("#merk1").val(merkTipe.split(" ")[0]).trigger("change");
                    $("#tipe1").val(merkTipe).trigger("change");
                    $("#tipe1").attr("disabled", true);
                } else {
                    $("#merk1").attr("disabled", false);
                    $("#tipe1").attr("disabled", false);
                    $("#merk1").val('').trigger("change");
                    $("#tipe1").val('').trigger("change");
                }
            },
            error: function () {
                $("#merk1").attr("disabled", false);
                $("#tipe1").attr("disabled", false);
                $("#merk1").val('').trigger("change");
                $("#tipe1").val('').trigger("change");
            }
        });
    }
});

$("#imei21, #imei22").on("keyup blur", function () {
    let imei = $(this).val();
    if (imei.length >= 8) {
        $.ajax({
            url: 'register-imei.html',
            type: 'POST',
            dataType: 'text',
            data: {
                txtContent: 'cekImei',
                txtImei: imei
            },
            success: function (response) {
                var data = JSON.parse(response);
                if (data.status === true) {
                    let merkTipe = data.dataTac.merkTipe;
                    $("#merk2").val(merkTipe.split(" ")[0]).trigger("change");
                    $("#tipe2").val(merkTipe).trigger("change");
                    $("#tipe2").attr("disabled", true);
                } else {
                    $("#merk2").attr("disabled", false);
                    $("#tipe2").attr("disabled", false);
                    $("#merk2").val('').trigger("change");
                    $("#tipe2").val('').trigger("change");
                }
            },
            error: function () {
                $("#merk2").attr("disabled", false);
                $("#tipe2").attr("disabled", false);
                $("#merk2").val('').trigger("change");
                $("#tipe2").val('').trigger("change");
            }
        });
    }
});


function getMerk(merk) {
    return merk.split(" ")[0];
}

$('#btnSubmitRegImei').click(function (e) {
    if ($('#flightNumber').val() === '') {
        alert('Flight Number belum diisi !');
    } else if ($('#flightNumber').val().length < 4 || $('#flightNumber').val().length > 25) {
        alert('Panjang karakter Flight Number tidak sesuai !');
    } else if ($('#dateArrival').val() === '') {
        alert('Date Arrival belum diisi !');
    } else if ($('#idIdentity').val() === '') {
        alert('Identity Type belum dipilih !');
    } else if (!numberOnly($('#idIdentity').val())) {
        alert('Identity Type tidak sesuai! Harap pilih dari data yang di sediakan.');
    } else if ($('#identityNumber').val() === '') {
        alert('Identity Number belum diisi !');
    } else if ($('#fullName').val() === '') {
        alert('Full Name belum diisi !');
    } else if (!letterOnly($('#fullName').val())) {
        alert('Full Name letter only !');
    } else if ($('#countryCode').val() === '') {
        alert('Harap klik Nationality berdasarkan negara yang ada di daftar ! ');
    } else if ($('#npwp').val() !== '' && $('#npwp').val().length !== 15) {
        alert('Panjang NPWP haruslah 15 digit!');
    } else if (!validateEmail($('#email').val())) {
        alert('Format email tidak baku !');
    } else if ($('#kdMerk1').val() === '' && $('#merk1').val() === '') {
        alert('Harap Inputkan Merk');
    } else if ($('#kdTipe1').val() === '' && $('#tipe1').val() === '') {
        alert('Harap inputkan Tipe');
//    } else if ($('#ram1').val() === '') {
//        alert('Harap inputkan RAM');
//    } else if ($('#kdKapasitas1').val() === '' && $('#kapasitas1').val() === '') {
//        alert('Harap inputkan Kapasitas');
//    } else if ($('#warna1').val() === '') {
//        alert('Harap inputkan Warna');
//    } else if ($('#warna1').val() === '') {
//        alert('Harap inputkan Warna');
    } else if ($('#imei11').val() === '' && $('#imei12').val() === '') {
        alert('Harap inputkan minimal 1 IMEI');
    } else if ($('#price1').val() === '') {
        alert('Harap inputkan Harga Barang');
    } else if ($('#txtCaptcha').val() === '') {
        alert('Key code belum diisi ! ');
    } else {
        $('#btnSubmitRegImei').addClass('disabled');
        $('#btnSubmitRegImei').html('Loading ...');
        $.ajax({
            url: 'register-imei.html',
            type: 'POST',
            dataType: 'text',
            data: {
                txtContent: 'sendImeiForm',
                txtCaptcha: $('#txtCaptcha').val(),
                txtFlightVoyage: $('#flightNumber').val(),
                txtDateArrival: $('#dateArrival').val(),
                txtTipeIndentitas: $('#idIdentity').val(),
                txtNomorIdentitas: $('#identityNumber').val(),
                txtNamaLengkap: $('#fullName').val(),
                txtKewarganegaraanId: $('#countryCode').val(),
                txtEmail: $('#email').val(),
                txtNpwp: $('#npwp').val(),
                txtMerk1: $('#kdMerk1').val(),
                txtMerkLain1: $('#merk1').val(),
                txtMerk2: $('#kdMerk2').val(),
                txtMerkLain2: $('#merk2').val(),
                txtTipe1: $('#kdTipe1').val(),
                txtTipeLain1: $('#tipe1').val(),
                txtTipe2: $('#kdTipe2').val(),
                txtTipeLain2: $('#tipe2').val(),
                txtRam1: $('#ram1').val(),
                txtRam2: $('#ram2').val(),
                txtStorage1: $('#kdKapasitas1').val(),
                txtStorageLain1: $('#kapasitas1').val(),
                txtStorage2: $('#kdKapasitas2').val(),
                txtStorageLain2: $('#kapasitas2').val(),
                txtColor1: $('#warna1').val(),
                txtColor2: $('#warna2').val(),
                txtImei11: $('#imei11').val(),
                txtImei12: $('#imei12').val(),
                txtImei21: $('#imei21').val(),
                txtImei22: $('#imei22').val(),
                txtCurrency1: $('#kdCurrency1').val(),
                txtCurrency2: $('#kdCurrency2').val(),
                txtPrice1: $('#price1').val(),
                txtPrice2: $('#price2').val(),
                txtFile: $('#txtFile').val()
            },
            success: function (response) {
                var res = response.split("|");
                if (res[0] === 'Success') {
                    alert('Success');
                    window.location.replace("https://www.beacukai.go.id/imei/scan-page/" + res[1] + ".html");
                } else {
                    if (res[0].includes("IMEI") || res[0].includes("key code")) {
                        alert(res[0]);
                    } else if (res[0].includes("duplicate")) {
                        alert('Data yang anda inputkan sudah pernah terdaftar. Silahkan hubungi petugas Pabean !');
                        window.location.replace("https://www.beacukai.go.id/imei/scan-page/" + res[1] + ".html");
                    } else {
                        if (res[0].startsWith("DOCTYPE html")) {
                            alert("Gagal Simpan. Harap coba lagi!");
                        } else {
                            alert(res[0]);
                        }
                    }
                }
                $('#txtFileInput').val('');
                $('#txtFile').val('');
                $('#btnSubmitRegImei').removeClass('disabled');
                $('#btnSubmitRegImei').html('Send');
            },
            error: function () {
                alert("Error, Registration Failed");
                $('#txtFileInput').val('');
                $('#txtFile').val('');
                $('#btnSubmitRegImei').removeClass('disabled');
                $('#btnSubmitRegImei').html('Send');
            }

        });
    }
});

$('#btnSubmitCekImei').click(function (e) {
    if ($('#cekImei').val() === '') {
        alert('Harap inputkan nomor IMEI');
    } else {
        $.ajax({
            url: 'cek-imei.html',
            type: 'post',
            dataType: 'text',
            data: {
                content: 'sendCekImei',
                txtCaptcha: $('#txtCaptcha').val(),
                txtImei: $('#cekImei').val()
            },
            success: function (response) {
                var res = response.split("|");
                if (res[1] === "Y") {
                    $("#hasilCekImei").html(
                        `<h5 style="color:blue" class="h5 text-center">
                            Data IMEI Telah Berhasil di kirim ke Database CEIR.
                        </h5>`
                    );
                } else {
                    if (res[0] === "wrongcaptcha") {
                        $("#hasilCekImei").html(`<h5 style="color:red" class="h5 text-center"> Pengisian key code tidak sesuai ! </h5>`);
                    } else if (res[0].indexOf("Tidak Valid") > -1) {
                        $("#hasilCekImei").html(`<h5 style="color:red" class="h5 text-center"> ${res[0]} </h5>`);
                    } else if (res[0] === "20") {
                        $("#hasilCekImei").html(`<h5 style="color:blue" class="h5 text-center"> Terdapat data Billing yang belum terbayar. Harap segera lakukan pembayaran Billing. </h5>`);
                    } else if (["10","50","60","70","30","40"].includes(res[0])) {
                        $("#hasilCekImei").html(`<h5 style="color:blue" class="h5 text-center"> IMEI dalam proses pengiriman ke Database CEIR.  </h5>`);
                    } else if (res[0] === "80") {
                        $("#hasilCekImei").html(`<h5 style="color:red" class="h5 text-center"> Data IMEI Invalid. </h5>`);
                    } else if (res[0] === "error") {
                        $("#hasilCekImei").html(`<h5 style="color:red" class="h5 text-center"> Mohon maaf saat ini layanan tidak dapat diakses atau merespons dengan benar.  </h5>`);
                    } else {
                        
                    }
                }
                reloadCaptcha();
            },
            error: function () {
                alert("Error, Cek IMEI Failed");
                reloadCaptcha();
            }
        });
    }
});


function populateTrackingResult(response) {
    var res = JSON.parse(response);
    var success = res.success;

    if (success === "true") {
        var details = res.detail;
        var panelContainer = '';
        for (var i = 0; i < details.length; i++) {

            var barang = details[i].barang;
            var barangContainer = "";
            var trBarang = '';
            if (barang !== undefined) {
                for (var b = 0; b < barang.length; b++) {
                    trBarang += '<tr valign="top"><td>' + barang[b].hsCode + '</td><td>' + barang[b].seri + '</td><td>' + barang[b].lartas + '</td></tr>';
                }
                barangContainer = '<header style="padding: 5px 15px">'
                        + '<h3 class="panel-title">INFORMASI BARANG</h3>'
                        + '</header><table class="table table-striped table-bordered">'
                        + '<tr valign="top"><th>Kode HS</th>'
                        + '<th>Seri</th>'
                        + '<th>Lartas</th>'
                        + '</tr>'
                        + trBarang
                        + '</table>';
            }

            var billing = details[i].detilbilling;

            var divBilling = '';
            var billingContainer = '';
            if (billing !== undefined) {
                for (var x = 0; x < billing.length; x++) {
                    divBilling += '<tr valign="top"><td>' + billing[x].akun + '</td><td>' + billing[x].jumlah + '</td></tr>';
                }

                billingContainer = '<header style="padding: 5px 15px">'
                        + '<h3 class="panel-title">TAGIHAN BILLING</h3>'
                        + '</header><table class="table table-striped table-bordered">'
                        + '<tr valign="top"><td>KODE BILLING</td><td>' + details[i].kodeBilling + '</td></tr>'
                        + '<tr valign="top"><td>TOTAL BILLING</td><td>' + details[i].totaleBilling + '</td></tr>'
                        + divBilling
                        + '</table>';
            } else {
                billingContainer = '<header style="padding: 5px 15px">'
                        + '<h3 class="panel-title">TAGIHAN BILLING - TIDAK DITEMUKAN</h3> </header>';
            }


            var status = details[i].status;
            var divStatus = '';
            var statusContainer = '';
            if (status !== undefined) {

                for (var j = 0; j < status.length; j++) {
                    divStatus += '<tr valign="top"><td>' + status[j].wk + '</td><td>' + status[j].kode + '</td><td>' + status[j].ket + '</td></tr>';
                }

                var statusContainer = '<header style="padding: 5px 15px">'
                        + '<h3 class="panel-title">HISTORY STATUS</h3>'
                        + '</header>'
                        + '<table class="table table-striped table-bordered">'
                        + divStatus
                        + '</table>';
            }

            var trackingContainer = '<table class="table table-striped table-bordered">'
                    + '<tr valign="top">'
                    + '<td>Pemberitahu</td><td>' + details[i].namaPemberitahu + '</td>'
                    + '</tr>'
                    + '<tr valign="top">'
                    + '<td>Penerima</td><td>' + details[i].namapenerima + '</td>'
                    + '</tr>'
                    + '<tr valign="top">'
                    + '<td>Pengirim</td><td>' + details[i].namapengirim + '</td>'
                    + '</tr>'
                    + '<tr valign="top">'
                    + '<td colspan="2">'
                    + '<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree' + i + '" class="buttoncel collapsed">'
                    + '<button class="btn-u btn-u-green col col-2"  style="float:right;"><i class="iconcl fa  fa-chevron-circle-down"></i> <span class="txclass">See Details</span></button>  '
                    + '</a>'
                    + '</td>'
                    + '</tr>'
                    + '</table>';

            var infoNoBarangContainer = '<header style="padding: 5px 15px">'
                    + '<h3 class="panel-title">INFORMASI NOMOR BARANG</h3>'
                    + '</header>'
                    + '<table class="table table-striped table-bordered">'
                    + '<tr valign="top">'
                    + '<td style="width:30%;">No Barang/AWB/Resi</td><td>' + details[i].noHouseAwb + '</td>'
                    + '</tr>'
                    + '<tr valign="top">'
                    + '<td>Tgl AWB</td><td>' + details[i].tglHouseAwb + '</td>'
                    + '</tr>'
                    + '</table>';

            //var collapseClass = (details.length==1) ? "collapse in" : "collapse" ;
            panelContainer += '   <div class="panel panel-default">'
                    + '<div class="panel-heading" style="background-color: #fcfcfc; padding: 0;">'
                    + trackingContainer
                    + '</div>'
                    + ' <div id="collapseThree' + i + '" class="panel-collapse collapse">'
                    + '<div class="panel-body" style="padding:0;">'
                    + infoNoBarangContainer
                    + barangContainer
                    + billingContainer
                    + statusContainer
                    + ' </div>'
                    + ' </div>'
                    + ' </div>';

        }

        //console.log('mypanecontainer'+panelContainer);
        $('#accordion').html(panelContainer);
        $('#infoBK').css("display", "none");
        $('#resultBKNew').css("display", "block");

    } else {
        $('#resultBKNew').css("display", "none");
        $('#infoBK').css("display", "block");
        $("#infoBK").html("Hasil Pencarian : " + res.message);
    }
}

function populateTrackingResult401(response) {

    var res = JSON.parse(response);
    var data = res.data;
    var panelContainer = '';

    for (var d = 0; d < data.length; d++) {
        console.log(data[d].nomorAju);
        var trackingAju = '<table class="table table-striped table-bordered">'
                + '<tr valign="top">'
                + '<td>' + data[d].nomorAju + '</td>'
                + '<td>'
                + '<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree1" class="buttoncel collapsed">'
                + '<button class="btn-u btn-u-green col"  style="float:right;"><i class="iconcl fa  fa-chevron-circle-down"></i> <span class="txclass">See Details</span></button>  '
                + '</a>'
                + '</td>'
                + '</tr>'
                + '</table>';
    }

    var infoNoBarangContainer = '<header style="padding: 5px 15px">'
            + '<h3 class="panel-title">INFORMASI NOMOR BARANG</h3>'
            + '</header>'
            + '<table class="table table-striped table-bordered">'
            + '<tr valign="top">'
            + '<td style="width:30%;">No Barang/AWB/Resi</td><td>' + res.nomorBarang + '</td>'
            + '</tr>'
            + '<tr valign="top">'
            + '<td style="width:30%;">No AJU</td><td>' + res.nomorAju + '</td>'
            + '</tr>'
            + '<tr valign="top">'
            + '<td>Tanggal </td><td>' + res.tanggalHouse + '</td>'
            + '</tr>'
            + '</table>';

    var status = res.statusTracking;
    var divStatus = '';
    var divdetailBilling = '';
    var statusContainer = '';
    if (status !== undefined) {

        for (var j = 0; j < status.length; j++) {
            if (status[j].kodeStatus === "303") {
                var detailBilling = status[j].detailBilling;
                for (var db = 0; db < detailBilling.length; db++) {
                    divdetailBilling += '<tr valign="top"><td style="width: 30%;">' + detailBilling[db].kodeAkun + ' - ' + detailBilling[db].namaAkun + '</td><td style="width: 70%;">' + detailBilling[db].nilai + '</td></tr>';
                }
//                divStatus += '<tr valign="top"><td>' + status[j].waktuRekam + '</td><td>' + status[j].kodeStatus + '</td>'
//                        + '<td>' + status[j].uraian + '</br>KODE BILING : ' + status[j].kodeBilling + '</br>TOTAL BILLING : ' + status[j].totalBilling + '</td></tr>'
//                        + '<tr valign="top"><td>&nbsp;</td><td>&nbsp;</td><td><table class="table table-striped table-bordered">' + divdetailBilling + '</table></td></tr>';
                divStatus += '<tr valign="top"><td>' + status[j].waktuRekam + '</td><td>' + status[j].kodeStatus + '</td>'
                        + '<td>' + status[j].uraian + '</br>KODE BILING : ' + status[j].kodeBilling + '</td></tr>';
            } else if (status[j].kodeStatus === "401") {
                var totalBm = status[j].totalBm !== undefined ? "Total Bea Masuk : " + status[j].totalBm + "</br>" : "";
                var totalBmad = status[j].totalBmad !== undefined ? "Total BMAD : " + status[j].totalBmad + "</br>" : "";
                var totalBmtp = status[j].totalBmtp !== undefined ? "Total BMTP : " + status[j].totalBmtp + "</br>" : "";
                var totalPpn = status[j].totalPpn !== undefined ? "Total PPn : " + status[j].totalPpn + "</br>" : "";
                var totalPph = status[j].totalPph !== undefined ? "Total PPh : " + status[j].totalPph + "</br>" : "";
                var totalPpnbm = status[j].totalPpnbm !== undefined ? "Total PPnBM : " + status[j].totalPpnbm + "</br>" : "";
                var totalTagihan = status[j].totalTagihan !== undefined ? "TOTAL TAGIHAN : " + status[j].totalTagihan + "</br>" : "";
                divStatus += '<tr valign="top"><td>' + status[j].waktuRekam + '</td><td>' + status[j].kodeStatus + '</td>'
                        + '<td>' + status[j].uraian + '</br>NOMOR SPPBMCP : ' + status[j].nomorSppbmcp + '</br>TANGGAL SPPBMCP : ' + status[j].tanggalSppbmcp + '</td></tr>'
                        + '<tr valign="top"><td>&nbsp;</td><td>&nbsp;</td><td>' + totalBm + totalBmad + totalBmtp + totalPpn + totalPph + totalPpnbm + totalTagihan + '</td></tr>';
            } else {
                divStatus += '<tr valign="top"><td>' + status[j].waktuRekam + '</td><td>' + status[j].kodeStatus + '</td><td>' + status[j].uraian + '</td></tr>';
            }
        }

        var statusContainer = '<header style="padding: 5px 15px">'
                + '<h3 class="panel-title">HISTORY STATUS</h3>'
                + '</header>'
                + '<table class="table table-striped table-bordered">'
                + divStatus
                + '</table>';
    }

    panelContainer += '   <div class="panel panel-default">'
            + '<div class="panel-heading" style="background-color: #fcfcfc; padding: 0;">'
            + trackingAju
            + '</div>'
            + ' <div id="collapseThree1" class="panel-collapse collapse">'
            + '<div class="panel-body" style="padding:0;">'
            + infoNoBarangContainer
//            + barangContainer
//            + billingContainer
            + statusContainer
            + ' </div>'
            + ' </div>'
            + ' </div>';

    $('#accordion').html(panelContainer);
    $('#infoBK').css("display", "none");
    $('#resultBKNew').css("display", "block");

}

function populateTrackingResult40(response) {

    var res = JSON.parse(response);

    var panelContainer = '';
    var trackingContainer = '<table class="table table-striped table-bordered">'
            + '<tr valign="top">'
            + '<td>Pemberitahu</td><td>' + res.namaPemberitahu + '</td>'
            + '</tr>'
            + '<tr valign="top">'
            + '<td>Penerima</td><td>' + res.namaPenerima + '</td>'
            + '</tr>'
            + '<tr valign="top">'
            + '<td>Pengirim</td><td>' + res.namaPengirim + '</td>'
            + '</tr>'
            + '<tr valign="top">'
            + '<td colspan="2">'
            + '<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree1" class="buttoncel collapsed">'
            + '<button class="btn-u btn-u-green col col-2"  style="float:right;"><i class="iconcl fa  fa-chevron-circle-down"></i> <span class="txclass">See Details</span></button>  '
            + '</a>'
            + '</td>'
            + '</tr>'
            + '</table>';

    var ntpn = res.ntpn !== null ? '<tr valign="top"><td>NTPN </td><td>' + res.ntpn + '</td></tr>' : "";
    var totalFob = res.totalFob !== null ? '<tr valign="top"><td>Total FOB </td><td>' + res.totalFob + '</td></tr>' : "";
    var totalFreight = res.totalFreight !== null ? '<tr valign="top"><td>Total Freight </td><td>' + res.totalFreight + '</td></tr>' : "";
    var totalInsurance = res.totalInsurance !== null ? '<tr valign="top"><td>Total Insurance </td><td>' + res.totalInsurance + '</td></tr>' : "";
    var totalCif = res.totalCif !== null ? '<tr valign="top"><td>Total CIF </td><td>' + res.totalCif + '</td></tr>' : "";
    var infoNoBarangContainer = '<header style="padding: 5px 15px">'
            + '<h3 class="panel-title">INFORMASI NOMOR BARANG</h3>'
            + '</header>'
            + '<table class="table table-striped table-bordered">'
            + '<tr valign="top">'
            + '<td style="width:30%;">No Barang/AWB/Resi</td><td>' + res.nomorBarang + '</td>'
            + '</tr>'
            + '<tr valign="top">'
            + '<td style="width:30%;">No AJU</td><td>' + res.nomorAju + '</td>'
            + '</tr>'
            + '<tr valign="top">'
            + '<td>Tanggal </td><td>' + res.tanggalHouse + '</td>'
            + '</tr>'
            + ntpn
            + totalFob
            + totalFreight
            + totalInsurance
            + totalCif
            + '</table>';

    var status = res.statusTracking;
    var divStatus = '';
    var divdetailBilling = '';
    var statusContainer = '';
    if (status !== undefined) {

        for (var j = 0; j < status.length; j++) {
            if (status[j].kodeStatus === "303") {
                var detailBilling = status[j].detailBilling;
                for (var db = 0; db < detailBilling.length; db++) {
                    divdetailBilling += '<tr valign="top"><td style="width: 30%;">' + detailBilling[db].kodeAkun + ' - ' + detailBilling[db].namaAkun + '</td><td style="width: 70%;">' + detailBilling[db].nilai + '</td></tr>';
                }
//                divStatus += '<tr valign="top"><td>' + status[j].waktuRekam + '</td><td>' + status[j].kodeStatus + '</td>'
//                        + '<td>' + status[j].uraian + '</br>KODE BILING : ' + status[j].kodeBilling + '</br>TOTAL BILLING : ' + status[j].totalBilling + '</td></tr>'
//                        + '<tr valign="top"><td>&nbsp;</td><td>&nbsp;</td><td><table class="table table-striped table-bordered">' + divdetailBilling + '</table></td></tr>';
                divStatus += '<tr valign="top"><td>' + status[j].waktuRekam + '</td><td>' + status[j].kodeStatus + '</td>'
                        + '<td>' + status[j].uraian + '</br>KODE BILING : ' + status[j].kodeBilling + '</td></tr>';
            } else if (status[j].kodeStatus === "401") {
                var totalBm = status[j].totalBm !== undefined ? "Total Bea Masuk : " + status[j].totalBm + "</br>" : "";
                var totalBmad = status[j].totalBmad !== undefined ? "Total BMAD : " + status[j].totalBmad + "</br>" : "";
                var totalBmtp = status[j].totalBmtp !== undefined ? "Total BMTP : " + status[j].totalBmtp + "</br>" : "";
                var totalPpn = status[j].totalPpn !== undefined ? "Total PPn : " + status[j].totalPpn + "</br>" : "";
                var totalPph = status[j].totalPph !== undefined ? "Total PPh : " + status[j].totalPph + "</br>" : "";
                var totalPpnbm = status[j].totalPpnbm !== undefined ? "Total PPnBM : " + status[j].totalPpnbm + "</br>" : "";
                var totalDenda = status[j].totalDenda !== undefined ? "Total Denda : " + status[j].totalDenda + "</br>" : "";
                var totalTagihan = status[j].totalTagihan !== undefined ? "TOTAL TAGIHAN : " + status[j].totalTagihan + "</br>" : "";
                divStatus += '<tr valign="top"><td>' + status[j].waktuRekam + '</td><td>' + status[j].kodeStatus + '</td>'
                        + '<td>' + status[j].uraian + '</br>NOMOR SPPBMCP : ' + status[j].nomorSppbmcp + '</br>TANGGAL SPPBMCP : ' + status[j].tanggalSppbmcp + '</td></tr>'
                        + '<tr valign="top"><td>&nbsp;</td><td>&nbsp;</td><td>' + totalBm + totalBmad + totalBmtp + totalPpn + totalPph + totalPpnbm + totalDenda + totalTagihan + '</td></tr>';
            } else if (status[j].kodeStatus === "307") {
                divStatus += '';
            } else if (status[j].kodeStatus === "405") {
                var ntpn = status[j].ntpn !== undefined ? "NTPN : " + status[j].ntpn + "</br>" : "";
                divStatus += '<tr valign="top"><td>' + status[j].waktuRekam + '</td><td>' + status[j].kodeStatus + '</td><td>' + status[j].uraian + '</br>' + ntpn + '</td></tr>';
            } else {
                divStatus += '<tr valign="top"><td>' + status[j].waktuRekam + '</td><td>' + status[j].kodeStatus + '</td><td>' + status[j].uraian + '</td></tr>';
            }
        }

        var statusContainer = '<header style="padding: 5px 15px">'
                + '<h3 class="panel-title">HISTORY STATUS</h3>'
                + '</header>'
                + '<table class="table table-striped table-bordered">'
                + divStatus
                + '</table>';
    }

    panelContainer += '   <div class="panel panel-default">'
            + '<div class="panel-heading" style="background-color: #fcfcfc; padding: 0;">'
            + trackingContainer
            + '</div>'
            + ' <div id="collapseThree1" class="panel-collapse collapse">'
            + '<div class="panel-body" style="padding:0;">'
            + infoNoBarangContainer
//            + barangContainer
//            + billingContainer
            + statusContainer
            + ' </div>'
            + ' </div>'
            + ' </div>';

    $('#accordion').html(panelContainer);
    $('#infoBK').css("display", "none");
    $('#resultBKNew').css("display", "block");

}

function populateTrackingResultBKB(response) {
    if (response.trim() !== "wrongcaptcha") {
        $('#txtCaptcha').val('');
        reloadCaptcha();
        var res = JSON.parse(response);
        var success = res.success;
        if (success === "true") {
            var details = res.detail;
            var panelContainer = '';
            for (var i = 0; i < details.length; i++) {
                var barang = details[i].barang;
                var barangContainer = "";
                var trBarang = '';
                if (barang !== undefined) {
                    for (var b = 0; b < barang.length; b++) {
                        trBarang += '<tr valign="top"><td>' + barang[b].hsCode + '</td><td>' + barang[b].seri + '</td><td>' + barang[b].lartas + '</td></tr>';
                    }
                    barangContainer = '<header style="padding: 5px 15px">'
                            + '<h3 class="panel-title">INFORMASI BARANG</h3>'
                            + '</header><table class="table table-striped table-bordered">'
                            + '<tr valign="top"><th>Kode HS</th>'
                            + '<th>Seri</th>'
                            + '<th>Lartas</th>'
                            + '</tr>'
                            + trBarang
                            + '</table>';
                }

                var billing = details[i].detilbilling;
                var divBilling = '';
                var billingContainer = '';
                if (billing !== undefined) {
                    for (var x = 0; x < billing.length; x++) {
                        divBilling += '<tr valign="top"><td>' + billing[x].akun + '</td><td>' + billing[x].jumlah + '</td></tr>';
                    }

                    billingContainer = '<header style="padding: 5px 15px">'
                            + '<h3 class="panel-title">TAGIHAN BILLING</h3>'
                            + '</header><table class="table table-striped table-bordered">'
                            + '<tr valign="top"><td>KODE BILLING</td><td>' + details[i].kodeBilling + '</td></tr>'
                            + '<tr valign="top"><td>TOTAL BILLING</td><td>' + details[i].totaleBilling + '</td></tr>'
                            + divBilling
                            + '</table>';
                } else {
                    billingContainer = '<header style="padding: 5px 15px">'
                            + '<h3 class="panel-title">TAGIHAN BILLING - TIDAK DITEMUKAN</h3> </header>';
                }

                var status = details[i].status;
                var divStatus = '';
                var statusContainer = '';
                if (status !== undefined) {

                    for (var j = 0; j < status.length; j++) {
                        divStatus += '<tr valign="top"><td>' + status[j].wk + '</td><td>' + status[j].kode + '</td><td>' + status[j].ket + '</td></tr>';
                    }

                    var statusContainer = '<header style="padding: 5px 15px">'
                            + '<h3 class="panel-title">HISTORY STATUS</h3>'
                            + '</header>'
                            + '<table class="table table-striped table-bordered">'
                            + divStatus
                            + '</table>';
                }

                var trackingContainer = '<table class="table table-striped table-bordered">'
                        + '<tr valign="top">'
                        + '<td>Pemberitahu</td><td>' + details[i].namaPemberitahu + '</td>'
                        + '</tr>'
                        + '<tr valign="top">'
                        + '<td>Penerima</td><td>' + details[i].namapenerima + '</td>'
                        + '</tr>'
                        + '<tr valign="top">'
                        + '<td>Pengirim</td><td>' + details[i].namapengirim + '</td>'
                        + '</tr>'
                        + '<tr valign="top">'
                        + '<td colspan="2">'
                        + '<a data-toggle="collapse" data-parent="#accordion" href="#collapseThree' + i + '" class="buttoncel collapsed">'
                        + '<button class="btn-u btn-u-green col col-2"  style="float:right;"><i class="iconcl fa  fa-chevron-circle-down"></i> <span class="txclass">See Details</span></button>  '
                        + '</a>'
                        + '</td>'
                        + '</tr>'
                        + '</table>';

                var infoNoBarangContainer = '<header style="padding: 5px 15px">'
                        + '<h3 class="panel-title">INFORMASI NOMOR BARANG</h3>'
                        + '</header>'
                        + '<table class="table table-striped table-bordered">'
                        + '<tr valign="top">'
                        + '<td style="width:30%;">No Barang/AWB/Resi</td><td>' + details[i].noHouseAwb + '</td>'
                        + '</tr>'
                        + '<tr valign="top">'
                        + '<td>Tgl AWB</td><td>' + details[i].tglHouseAwb + '</td>'
                        + '</tr>'
                        + '</table>';

                //var collapseClass = (details.length==1) ? "collapse in" : "collapse" ;
                panelContainer += '   <div class="panel panel-default">'
                        + '<div class="panel-heading" style="background-color: #fcfcfc; padding: 0;">'
                        + trackingContainer
                        + '</div>'
                        + ' <div id="collapseThree' + i + '" class="panel-collapse collapse">'
                        + '<div class="panel-body" style="padding:0;">'
                        + infoNoBarangContainer
                        + barangContainer
                        + billingContainer
                        + statusContainer
                        + ' </div>'
                        + ' </div>'
                        + ' </div>';
            }
            //console.log('mypanecontainer'+panelContainer);
            $('#accordion').html(panelContainer);
            $('#infoBKB').css("display", "none");
            $('#resultBKBNew').css("display", "block");
        } else {
            $('#resultBKBNew').css("display", "none");
            $('#infoBKB').css("display", "block");
            $("#infoBKB").html("Hasil Pencarian : " + res.message);
        }
    } else {
        $('#resultBKBNew').css("display", "none");
        $('#infoBKB').css("display", "block");
        $("#infoBKB").html("<div style=\"color:red\"> Pengisian key code tidak sesuai ! </div>");
    }
}

(function ($) {
    /**
     * Set your date here  (YEAR, MONTH (0 for January/11 for December), DAY, HOUR, MINUTE, SECOND)
     **/
    var launch = new Date(2022, 3, 1, 0, 0, 0);
    var extraTime = new Date(2022, 3, 10, 0, 0, 0);
    /**
     * The script
     **/
    var days = $('#days');
    var hours = $('#hours');
    var minutes = $('#minutes');
    var seconds = $('#seconds');

    setDate();
    function setDate() {
        var now = new Date();
        if (launch < now) {
            if (extraTime < now) {
                $('#counter-page').attr("style", "display: none");
            } else {
                $('#isi-page').html("<div class='title-v1'><h2>Buku Tarif Kepabeanan Indonesia (BTKI) 2022 resmi diberlakukan mulai 1 April 2022.</h2><br/><br/>Sesuai dengan Peraturan Menteri Keuangan <a href='https://jdih.kemenkeu.go.id/in/dokumen/peraturan/b1ff6116-ce13-4ba2-676d-08da113e5408' target='_blank'>Nomor 26/PMK.010/2022</a><br/>tentang Penetapan Sistem Klasifikasi Barang dan Pembebanan Tarif Bea Masuk atas Barang Impor</div>");
                setTimeout(setDate, 1000);
            }
        } else {
            var s = (launch.getTime() - now.getTime()) / 1000;
            var d = Math.floor(s / 86400);
            days.html('<h1>' + d + '</h1><p>Day' + (d > 1 ? 's' : ''), '</p>');
            s -= d * 86400;

            var h = Math.floor(s / 3600);
            hours.html('<h1>' + h + '</h1><p>Hour' + (h > 1 ? 's' : ''), '</p>');
            s -= h * 3600;

            var m = Math.floor(s / 60);
            minutes.html('<h1>' + m + '</h1><p>Minute' + (m > 1 ? 's' : ''), '</p>');

            s = Math.floor(s - m * 60);
            seconds.html('<h1>' + s + '</h1><p>Second' + (s > 1 ? 's' : ''), '</p>');
            setTimeout(setDate, 1000);
        }
    }
})(jQuery);
