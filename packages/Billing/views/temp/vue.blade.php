<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title></title>
        <script src="{!! asset('bower_components/vue/dist/vue.min.js') !!}"></script>
        <script src="{!! asset('js/main.js') !!}"></script>
    </head>
    <body>
        <div id="demo">
            <p>@{{message}}</p>
            <input v-model="message">
        </div>

        <script type="text/javascript">
        var demo = new Vue({
                        el: '#demo',
                        data: {
                            message: 'Hello Vue.js!'
                        }
                    });
        </script>
    </body>
</html>
