<?php

namespace Uol\BoaCompra\Helper\Hosted;

class RequestAPayment
{
    /**
     * @var array
     */
    private $data;

    /**
     * RequestAPayment constructor.
     * @param array $data
     */
    public function __construct(
        $data = []
    ) {
        $this->data = $data;
    }

    public function execute()
    {
        echo '
        <body>
            <script>
            function post(path, params) {
                var form = document.createElement("form");
                form.setAttribute("method", "post");
                form.setAttribute("action", path);
                for(var key in params) {
                    if(params.hasOwnProperty(key)) {
                        var hiddenField = document.createElement("input");
                        hiddenField.setAttribute("type", "hidden");
                        hiddenField.setAttribute("name", key);
                        hiddenField.setAttribute("value", params[key]);
                        form.appendChild(hiddenField);
                    }
                }
                document.body.appendChild(form);
                form.submit();
            }
            post("https://billing.boacompra.com/payment.php", ' . json_encode($this->data) . ');
            </script>
        </body>
        ';
    }
}
