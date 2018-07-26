require(['jquery'], function($) {
    $(document).ready(function () {

        if ($("#selected-co-authors option").length == 0){
            $("#remove-co-author").attr("disabled", true);
        }

        if ($("#available-co-authors option").length == 0){
            $("#add-co-author").attr("disabled", true);
        }

        $("#add-co-author").on('click',function() {
            moveSelectedOptions("available-co-authors", "selected-co-authors");

            if ($("#selected-co-authors option").length > 2) {
                $(".too-many-alert").show();
                $("#add-co-author").attr("disabled", true);
            }
            if ($("#selected-co-authors option").length > 0) {
                $("#remove-co-author").attr("disabled", false);
            }
            if ($("#available-co-authors option").length == 0) {
                $("#add-co-author").attr("disabled", true);
            }

        });

        $("#remove-co-author").on('click',function() {
            moveSelectedOptions("selected-co-authors","available-co-authors");

            if ($("#selected-co-authors option").length <= 2) {
                $(".too-many-alert").hide();
                $("#add-co-author").attr("disabled", false);
            }
            if ($("#selected-co-authors option").length == 0) {
                $("#remove-co-author").attr("disabled", true);
            }
        });

        /**
         * Compares two <option> element texts
         *
         * @param a
         * @param b
         * @returns {number}
         */
        function compareOptionText(a,b) {
            /*
             * return >0 if a>b
             *         0 if a=b
             *        <0 if a<b
             */
            // textual comparison
            return a.text!=b.text ? a.text<b.text ? -1 : 1 : 0;
            // numerical comparison
            //  return a.text - b.text;
        }

        /**
         * Sorts options of a <select> element
         *
         * @param list
         */
        function sortOptions(list) {
            var items = list.options.length;
            // create array and make copies of options in list
            var tmpArray = new Array(items);
            for (var i=0; i<items; i++ )
                tmpArray[i] = new
                Option(list.options[i].text,list.options[i].value);
            // sort options using given function
            tmpArray.sort(compareOptionText);
            // make copies of sorted options back to list
            for ( i=0; i<items; i++ )
                list.options[i] = new Option(tmpArray[i].text,tmpArray[i].value);

        }

        /**
         * Moves selected options of on <select> element to another
         *
         * @param from
         * @param to
         */
        function moveSelectedOptions(from, to){
            var m1 = document.getElementById(from);
            var m2 = document.getElementById(to);

            var m1len, m2len, i;
            m1len = m1.length;
            for ( i=0; i<m1len ; i++){
                if (m1.options[i].selected == true ) {
                    m2len = m2.length;
                    m2.options[m2len]= new Option(m1.options[i].text);
                }
            }

            for ( i = (m1len -1); i>=0; i--){
                if (m1.options[i].selected == true ) {
                    m1.options[i] = null;
                }
            }
            var selElem = document.getElementById(to);
            sortOptions(selElem);
            console.log(selElem);
        }
    });
});