(function ($) {
    $.fn.dynamicModal = function (options) {
        var  opt  = $.extend({
            id:'modal',
            cls:'',
            title:'Test Modal',
            content:'',
            buttons:[
            {
                cancel:true,
                text:'Cancel',
                id:'cancel',
                cls:'btn-default'
            }]
        },options)
        var modal = create_modal(opt);
        $(this).append(modal);
        
        if(opt.datetimepicker){
            var selector = '#'+opt.id+'-body';
           $(selector).datetimepicker(opt.datetimepicker).on('changeDate', function(ev){
                $('.modal').modal('hide'); 
            });  
        }
        
        function create_modal(opt){
            var modal ='';
            modal +='<div class="modal fade ';
            if(opt.cls) modal += opt.cls;
            modal +='"';
            if(opt.id)  modal +='id="'+opt.id+'" ';
            modal +=' role="dialog">';
            modal +='<div class="modal-dialog">';
            modal +='<div class="modal-content">';
            modal +='<div class="modal-header">';
            modal +='<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true" class="glyphicon glyphicon-remove"></span><span class="sr-only"></span></button>';
               
            if(opt.title) modal +='<h2>'+opt.title+'</h2>';
               
            modal +='</div>';
            modal +='<div class="modal-body"';
            if(opt.id)  modal +='id="'+opt.id+'-body"';
            modal +='>'
            if(opt.content) modal += opt.content;
            modal +='</div>';
            if(opt.buttons){
                modal +='<div class="modal-footer">';
                for(var i = 0; i < opt.buttons.length; i++ ){
                    modal +='<button type="button" ';
                    if(opt.buttons[i].cancel) modal +='data-dismiss="modal" ';
                    if(opt.buttons[i].cls) modal +='class="btn '+opt.buttons[i].cls+'" ';
                    if(opt.buttons[i].id) modal +='id="'+opt.buttons[i].id+'" ';
                    modal +='>';
                    if(opt.buttons[i].text) modal += opt.buttons[i].text;
                    else modal += 'Button';
                    modal +='</button>';
                }
                modal +='</div>';
            }
            modal +='</div>';
            modal +='</div>';
            modal +='</div>';
            return modal;
        }
    }
}(jQuery));


