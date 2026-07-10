<script>
    $(document).ready(function () {
        $.ajax({
            url: "{{ route('frontend.categories') }}",
            method: "get",
            dataType: "json",
            success: function (data) {
                $.each(data.items, function (key, item) {
                    let checkboxId = 'category-' + item.id;
                    let checkBox = ''
                        + '<div class="form-check mb-2">'
                        + '<input checked="checked" class="form-check-input" id="' + checkboxId + '" name="categoryId[]" type="checkbox" value="' + item.id + '"> '
                        + '<label class="form-check-label" for="' + checkboxId + '">'
                        + item.name
                        + '</label>'
                        + '</div>';

                    $(checkBox).prependTo('#addsub');
                });
            }
        });

        $(document).on("click", "#sub", function () {
            let arr = $("#addsub").serializeArray();
            let aParams = [];
            let sParam;

            for (var i = 0, count = arr.length; i < count; i++) {
                sParam = encodeURIComponent(arr[i].name);
                sParam += "=";
                sParam += encodeURIComponent(arr[i].value);
                aParams.push(sParam);
            }

            sParam = 'action';
            aParams.push(sParam);

            let sendData = aParams.join("&");

            $.ajax({
                url: "{{ route('frontend.addsub') }}",
                method: "POST",
                data: sendData,
                dataType: "json",
                success: function (data) {
                    if (data.result != null) {
                        let alert_msg = '';

                        if (data.result == 'success') {
                            alert_msg += '<div class="alert alert-success alert-dismissible fade show">';
                            alert_msg += '<button class="btn-close" aria-label="Close" data-bs-dismiss="alert" type="button"></button>';
                            alert_msg += data.msg;
                            alert_msg += '</div>';
                        } else if (data.result == 'errors') {
                            $.each(data.msg, function (index, val) {
                                alert_msg += '<div class="alert alert-danger alert-dismissible fade show">';
                                alert_msg += '<button class="btn-close" aria-label="Close" data-bs-dismiss="alert" type="button"></button>';

                                let arr = data.msg[index];
                                alert_msg += '<ul>';

                                for (var i = 0; i < arr.length; i++) {
                                    alert_msg += '<li>' + arr[i] + '</li>';
                                }

                                alert_msg += '</ul>';
                                alert_msg += '</div>';
                            });
                        }

                        $("#resultSub").html(alert_msg);
                    }
                }
            });
        });
    });

</script>
