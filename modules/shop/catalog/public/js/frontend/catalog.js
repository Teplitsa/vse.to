/**
 * Initialize
 */
$(function(){

    // ----- Sections tree
    // Initialize toggle tree branches controls
    $('.tree').each(function(i, tree){

        // Obtain tree id - this would be used as prefix to control and branch ids
        var prefix = tree.id;
        var loaded = {};

        /**
         * Detect all clicks within the tree, determine what was clicked by event.target
         * If a control was clicked then toggle corresponding branch
         */
        $(tree).click(function(event){
            var re = new RegExp(prefix + '_toggle_(.*)');
            var matches = event.target.id.match(re);

            if (matches)
            {
                // "toggle" bullet was clicked
                var toggle = $(event.target);
                var branch_path = matches[1];

                var branch = $('#' + prefix + '_branch_' + branch_path);

                branch_path = branch_path.replace('__','/');
                var url = branch_toggle_url.replace('{{path}}', branch_path);

                if (branch.hasClass('folded'))
                {
                    // Unfold (show) the branch
                    toggle.removeClass('toggled_off');
                    branch.removeClass('folded').show();

                    if ( ! loaded[branch_path])
                    {
                        // If branch is empty - load its contents via ajax request                        
                        branch.html('Loading...');
                        $.get(url.replace('{{toggle}}', 'on'), null, function(response){
                            loaded[branch_path] = 1;
                            branch.html(response);
                        });
                    }
                    else
                    {
                        // If not empty - simply peform an ajax request to save branch state in session
                        $.get(url.replace('{{toggle}}', 'on'));
                    }
                }
                else
                {
                    // Fold (hide) the branch
                    toggle.addClass('toggled_off');
                    branch.addClass('folded').hide();

                    // Mark as loaded
                    loaded[branch_path] = 1;

                    // Peform an ajax request to save branch state in session
                    $.get(url.replace('{{toggle}}', 'off'));
                }
                
                event.preventDefault();
            }

        });
    });
});