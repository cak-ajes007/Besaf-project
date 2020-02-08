<script src="<?= base_url() ?>assets/Front/js/jquery.js"></script>
<script src="<?= base_url() ?>assets/Front/js/bootstrap.js"></script>
<script src="<?= base_url() ?>assets/Front/js/all.js"></script>
<script src="<?= base_url() ?>assets/Front/js/script.js"></script>

<!-- formjs -->
<script>
    $(document).ready(function() {
        // ! VERIFIKASI EMAIL
        data = $('input.kode').val();
        if (data !== '') {

            $.ajax({
                method: "POST",
                url: "<?= base_url('api/user/verify') ?>",
                data: {
                    verificationcode: data
                },
                success: function(response) {
                    pesan = response.message
                    if (response.error == true) {
                        notif("div.muncul-pesan", 'warning', pesan)
                    } else {
                        $('div.muncul-pesan').html('')
                        notif('div.muncul-pesan', 'success', pesan)
                    }
                }
            });
        }

        // LOGIN ajax
        $(document).on('submit', 'form.form-login', function() {
            usernameEmail = $('input.usernameEmail').val()
            password = $('input.password').val()

            $.ajax({
                    url: "<?= base_url('api/user/login'); ?>",
                    method: 'POST',
                    data: {
                        usernameEmail: usernameEmail,
                        password: password
                    },
                })
                .done(function(req) {
                    pesan = req.message
                    if (req.error == true) {
                        notif("div.muncul-pesan", "warning", pesan)
                    } else {
                        $('div.muncul-pesan').html('')
                        user_id = req.data.id
                        role_id = req.data.role_id
                        username = req.data.username
                        window.location = "<?= base_url('auth/session/'); ?>" + role_id + "/" + username + "/" + user_id
                    }
                })
            return false
        })
        // REGISTRASI Ajax
        $(document).on('submit', 'form.form-daftar', function() {
            fullname = $('input.fullname').val()
            username = $('input.username').val()
            email = $('input.email').val()
            password = $('input.regpassword').val()
            verifypassword = $('input.verifypassword').val()
            country = $('select.country').val()

            $.ajax({
                    url: "<?= base_url('api/user/registrasi'); ?>",
                    method: 'POST',
                    data: {
                        username: username,
                        fullname: fullname,
                        password: password,
                        verifypassword: verifypassword,
                        email: email,
                        country: country
                    },
                })
                .done(function(req) {
                    pesan = req.message
                    if (req.error == true) {
                        notif('div.muncul-pesan-daftar', 'warning', pesan)
                    } else {
                        notif('div.muncul-pesan', 'success', pesan)
                        $('div.muncul-pesan-daftar').val('')
                        $('input.username').val('')
                        $('input.fullname').val('')
                        $('input.email').val('')
                        $('input.regpassword').val('')
                        $('input.verifypassword').val('')
                        changeTab('login')
                    }
                })
            return false
        })

        $.ajax({
                url : '<?php echo base_url('api/Turnamen/show') ?>',
                method : "POST",
                success : function(req) {
                    html = '';
                    $.each(req.data, function(index,obj) {
                        if (obj.date_end >= '<?php echo date('Y-m-d') ?>') {
                            status = '<label class="badge badge-danger">On going</label>';
                        } else {
                            status = '<label class="badge badge-secondary">End</label>'
                        }
                            html += '\
                            <a href="<?= base_url('auth/tournament_details/') ?>'+obj.id+'" class="text-decoration-none">\
                            <div class="card bg-dark rounded text-white card-hover">\
                                <div class="row no-gutters">\
                                    <div class="col-md-4 col-xl-3" align="center">\
                                        <img src="<?php echo base_url('api/img/turnamen/') ?>'+obj.image+'" alt="" class="rounded-left" style="max-width: 200px; max-height: 200px">\
                                    </div>\
                                    <!-- end Image Tournament -->\
                                    <div class="col-md col-xl">\
                                        <div class="card-body">\
                                            <div class="row no-gutters d-iniline-block border-bottom border-secondary">\
                                                <!-- title tournament -->\
                                                <h5 class="card-title title-di-mobile-tournament">'+obj.nama+'</h5>\
                                                <!-- end title tournament -->\
                                                <!-- status -->\
                                                <div class="ml-auto">\
                                                    '+status+'\
                                                </div>\
                                                <!-- end status -->\
                                            </div>\
                                            <div class="p-0 m-0">\
                                                <div class="row no-gutters pt-3 text-mobile-sm">\
                                                    <!-- list -->\
                                                    <div class="col-md col">\
                                                        <h5 class="text-secondary text-mobile">Prize</h5>\
                                                        <p>'+obj.hadiah+'</p>\
                                                    </div>\
                                                    <div class="col-md col">\
                                                        <h5 class="text-secondary text-mobile">Slots</h5>\
                                                        <p>'+obj.slots+'</p>\
                                                    </div>\
                                                    <div class="col-md col">\
                                                        <h5 class="text-secondary text-mobile">Date</h5>\
                                                        <p>'+obj.date_start+'</p>\
                                                    </div>\
                                                    <div class="col-md col">\
                                                        <h5 class="text-secondary text-mobile">Time</h5>\
                                                        <p>'+obj.time+'</p>\
                                                    </div>\
                                                    <!-- end list -->\
                                                </div>\
                                            </div>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                            </a>\
                            ';
                    })
                    $('div.tournament').html(html);
                }
            })

        $.ajax({
            url : "<?php echo base_url('api/Turnamen/info'); ?>",
            method : "POST",
            data : {
                turnamen_id : '<?php echo $this->session->userdata('tournament_id') ?>'
            },
            success : function(req) {
                console.log(req);
                $('img.turnamen_image').attr('src','<?php echo base_url('api/img/turnamen/') ?>'+req.data.image);
                $('p.date_start').html('Until '+req.data.date_start);
                $('p.slots').html(req.data.slots);
                $('p.host').html(req.data.komunitas_id);
                if (req.data.mode=='1') {
                    mode = 'Group Stage';
                } else {
                    mode = "Knock Out";
                }
                $('p.mode').html(mode);
                if (req.data.venue=='1') {
                    venue = 'Offline';
                } else {
                    venue = 'Online';
                }
                $('p.venue').html(venue+' tournament');
                $('div.deskripsi').html(req.data.informasi);
                $('p.hadiah').html('Rp. '+req.data.hadiah);
                $('div.rules').html(req.data.rules);
                $('div.to_join').html(req.data.how_to_join);
                $('h4.nama').html(req.data.nama)
            }
        })

        // notifikasi
        function notif(element, type, message) {
            $(element).html('\
				<div class="alert alert-' + type + ' mb-4 mt-0 w-100 shadow rounded-0" role="alert">\
			        ' + message + '\
			    </div>\
			    ');
        }
    });
</script>
</body>

</html>