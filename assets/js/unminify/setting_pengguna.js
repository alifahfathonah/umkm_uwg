$("#form").validate({
	errorElement: "span",
	errorPlacement: (err, el) => {
		err.addClass("invalid-feedback"), el.closest(".form-group").append(err);
	},
	submitHandler: () => {
        const pass = $('[name="password"]').val();
        const konf_pass = $('[name="konf_password"]').val();

        if (pass != konf_pass){
            Swal.fire("Gagal", "Password & Konfirmasi Password berbeda", "warning");
            return;
        }

        $.ajax({
            url: editUrl,
            type: "post",
            dataType: "json",
            data: $("#form").serialize(),
            success: (res) => {
                if (res.success) {
                    $(".modal").modal("hide");
                    Swal.fire("Sukses", res.message, "success");
                } else {
                    Swal.fire("Gagal", res.message, "warning");
                }
            },
            error: (err) => {
                console.log(err);
            },
        });
	},
});