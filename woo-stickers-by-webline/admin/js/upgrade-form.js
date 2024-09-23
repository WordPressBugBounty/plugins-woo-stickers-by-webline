jQuery(document).ready(function ($) {

    const dropdown = document.getElementById('wosbw_saved_keyword');
    const linkPlaceholder = document.getElementById('wosbw-dynamic-link');
    const copyButton = document.getElementById('wosbw-copy-button');

    function updateLinkWosbw() {
        const selectedValue = dropdown?.value;
        if (selectedValue) {
            let parts = selectedValue?.split('|');
            let trimmedValue = parts[0];
            const selectedText = dropdown?.options[dropdown.selectedIndex].text;
            if (linkPlaceholder) {
                linkPlaceholder.textContent = '<a href="' + trimmedValue + '">' + selectedText + '</a>';
            }
        }
    }

    function copyLinkWosbw() {
        event.preventDefault();
        const range = document.createRange();
        range.selectNode(linkPlaceholder);
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand('copy');
        window.getSelection().removeAllRanges();
    }

    updateLinkWosbw();
    if (dropdown) {
        dropdown.addEventListener('change', updateLinkWosbw);
    }
    if (copyButton) {
        copyButton.addEventListener('click', copyLinkWosbw);
    }

    $('#wosbw-copy-button').click(function () {
        var svg = document.querySelector('.wosbw-copy-btn svg');
        var originalSVG = svg.innerHTML;
        svg.innerHTML = `<svg clip-rule="evenodd" fill-rule="evenodd" image-rendering="optimizeQuality" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" 
                            viewBox="0 0 2.54 2.54" xmlns="http://www.w3.org/2000/svg" id="fi_14025310"><g id="图层_x0020_1">
                            <circle cx="1.27" cy="1.27" fill="#48b02c" r="1.27"></circle><g fill="#fff">
                            <path d="m.96229 1.62644.8951-.89509c.02637-.02638.06967-.02611.09578 0l.08642.08642c.02611.02611.02611.06968 0 .09578l-.89509.8951c-.02611.02611-.06941.02638-.09579 0l-.08642-.08642c-.02638-.02638-.02638-.06941 0-.09579z"></path>
                            <path d="m.6827 1.08089.54525.54525c.02637.02638.02606.06973 0 .09579l-.08642.08642c-.02606.02605-.06973.02605-.09579 0l-.54525-.54525c-.02606-.02606-.02637-.06941 0-.09579l.08642-.08642c.02638-.02637.06941-.02637.09579 0z"></path>
                            </g></g></svg>`;
        setTimeout(function() {
            svg.innerHTML = originalSVG;
        }, 3000);
    });

    // For Upgrade to Premium Page
    var backlinkRadio = document.querySelector('input[name="wosbw_upgrade_option"][value="backlink"]');
    var premiumRadio = document.querySelector('input[name="wosbw_upgrade_option"][value="premium"]');
    var backlinkDiv = document.getElementById('backlink');
    var premiumDiv = document.getElementById('premium');
    const validateButtonDiv = document.getElementById('validate-button');

    function showHideDiv() {
        if (backlinkRadio?.checked) {
            backlinkDiv.style.display = 'block';
            premiumDiv.style.display = 'none';
        } else if (premiumRadio?.checked) {
            backlinkDiv.style.display = 'none';
            premiumDiv.style.display = 'block';
        } else {
            if (backlinkDiv) {
                backlinkDiv.style.display = 'block';
            }
            if (premiumDiv) {
                premiumDiv.style.display = 'none';
            }
        }
    }

    backlinkRadio?.addEventListener('change', showHideDiv);
    premiumRadio?.addEventListener('change', showHideDiv);
    showHideDiv();

    if (premiumRadio) {
        premiumRadio.addEventListener('change', function () {
            if (this.checked) {
                validateButtonDiv.style.display = 'none';
            }
        });
    }
    if (backlinkRadio) {
        backlinkRadio.addEventListener('change', function () {
            if (this.checked) {
                validateButtonDiv.style.display = 'block';
            }
        });
    }

    // To fetch saved post title in input box
    function fetchPostTitle() {
        var permalink = document.getElementById('selected_post_permalink_wosbw_wosbw')?.value;
        var data = {
            'action': 'get_post_title_wosbw',
            'permalink': permalink
        };
        jQuery.post(ajaxurl, data, function(response) {
            document.getElementById('select_posts_input_wosbw')?.setAttribute('value', response);		
        });
    }
    fetchPostTitle();

});

jQuery(document).ready(function ($) {
    $('#select_posts_input_wosbw').autocomplete({
        source: function (request, response) {
            $.ajax({
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'search_posts_pages',
                    term: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $('#select_posts_input_wosbw').val(ui.item.label);
            $('#selected_post_permalink_wosbw').val(ui.item.permalink);
            return false;
        }
    });
});

jQuery(document).ready(function ($) {

    var pop_up_box_upgrade = document.getElementById("pop-up-box-upgrade");
    const elementsToBlur = ["content","wosbw-pricing-cards","content-inside","select-page-div","validate-btn","wosbw-plans-p","wosbw-heading","right-box-xmlsbw"];
    if(pop_up_box_upgrade){
        pop_up_box_upgrade.style.display = "none";
    }

    $('.close-popup-wosbw').click(function () {
        event.preventDefault();
        pop_up_box_upgrade.style.display = "none";
        elementsToBlur.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.style.filter = "none";
                element.style.pointerEvents = "auto";
            }
        });
    });

    jQuery('#wosbw-upgrade-to-premium').submit(function (event) {
        event.preventDefault();
        var inputValue = $('#select_posts_input_wosbw').val().trim();
        if (inputValue != '') {
            var page_url = document.getElementById('selected_post_permalink_wosbw').value;
            var page_name = document.getElementById('select_posts_input_wosbw').value;
            var keyword_value = document.getElementById('keyword_value').value;
            var formData = $(this).serialize();
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                    action: 'wosbw_save_upgrade_option',
                    formData: formData,
                    title: inputValue
                },
                beforeSend: function () {
                    $('.loader').css('display', 'block');
                },
                success: function (response) {
                    $('.loader').css('display', 'none');
                    if(response.data.valid){
                        if (response.success) {
                            $('#wosbw-heading').text('Pro Access Enabled').css('color','#4AB01A');
                            $('.content').css('border-top', '3px solid #4AB01A');
                            $('.content .content-inside1 .wosbw-right-des ul li').css('background-color', 'white');
                            $('.content .content-inside1 .wosbw-right-des ul li').css('border', 'border: 0.5px solid #4AB01A');
                            var svg = document.querySelectorAll('.content .content-inside1 .wosbw-right-des ul li svg');
                            svg.forEach(function(svgElement) {
                                svgElement.innerHTML = `<g mask="url(#mask0_4_9)">
                                                <path d="M16.5869 7.49821L15.4275 6.33898C15.1523 6.06381 14.9268 5.52001 14.9268 5.13004V3.4906C14.9268 2.71072 14.2897 2.07363 13.51 2.07321H11.8699C11.4804 2.07321 10.936 1.8473 10.6608 1.57233L9.50158 0.413107C8.95077 -0.137702 8.04892 -0.137702 7.49811 0.413107L6.33888 1.57316C6.06346 1.84834 5.51842 2.07363 5.12973 2.07363H3.49029C2.71145 2.07363 2.07353 2.71072 2.07353 3.4906V5.13008C2.07353 5.51852 1.84807 6.06402 1.57285 6.33903L0.413418 7.49825C-0.137806 8.04906 -0.137806 8.95092 0.413418 9.5026L1.57285 10.6618C1.84824 10.937 2.07353 11.4823 2.07353 11.8708V13.5103C2.07353 14.2893 2.71145 14.9272 3.49029 14.9272H5.12977C5.51929 14.9272 6.06371 15.1527 6.33892 15.4277L7.49815 16.5873C8.04896 17.1377 8.95082 17.1377 9.50163 16.5873L10.6608 15.4277C10.9362 15.1525 11.4804 14.9272 11.87 14.9272H13.5101C14.2897 14.9272 14.9268 14.2893 14.9268 13.5103V11.8708C14.9268 11.4806 15.1525 10.9368 15.4275 10.6618L16.5869 9.5026C17.1373 8.95092 17.1373 8.04902 16.5869 7.49821ZM7.3753 11.6877L4.24958 8.5616L5.25133 7.56005L7.37555 9.68426L11.7482 5.31261L12.7497 6.31416L7.3753 11.6877Z" fill="#ABF7B1"/>
                                                </g>`;
                            });
                            $('.success-message').html("").css({ 'display': 'none'});
                            elementsToBlur.forEach(id => {
                                const element = document.getElementById(id);
                                if (element) {
                                    element.style.filter = "blur(3px)";
                                    element.style.pointerEvents = "none";
                                }
                            });
                            pop_up_box_upgrade.style.display = "block";                         
                        } else {
                            $('#wosbw-heading').text('Upgrade to Pro Features').css('color', '#1d2327');
                            $('.content').css('border-top', '3px solid #FDB930');
                            $('.content .content-inside1 .wosbw-right-des ul li').css('background-color', '#fffbf3');
                            $('.content .content-inside1 .wosbw-right-des ul li').css('border', 'border: 0.5px solid #ffeecb');
                            var svg = document.querySelectorAll('.content .content-inside1 .wosbw-right-des ul li svg');
                            svg.forEach(function(svgElement) {
                                svgElement.innerHTML = `<g clip-path="url(#clip0_1642_43)">
                                <path d="M16.5871 7.49821L15.4277 6.33898C15.1525 6.06381 14.927 5.52001 14.927 5.13004V3.4906C14.927 2.71072 14.2899 2.07363 13.5102 2.07321H11.8701C11.4806 2.07321 10.9362 1.8473 10.661 1.57233L9.50173 0.413107C8.95092 -0.137702 8.04907 -0.137702 7.49826 0.413107L6.33903 1.57316C6.06361 1.84834 5.51857 2.07363 5.12988 2.07363H3.49044C2.7116 2.07363 2.07368 2.71072 2.07368 3.4906V5.13008C2.07368 5.51852 1.84822 6.06402 1.573 6.33903L0.413571 7.49825C-0.137653 8.04906 -0.137653 8.95092 0.413571 9.5026L1.573 10.6618C1.84839 10.937 2.07368 11.4823 2.07368 11.8708V13.5103C2.07368 14.2893 2.7116 14.9272 3.49044 14.9272H5.12992C5.51944 14.9272 6.06386 15.1527 6.33907 15.4277L7.4983 16.5873C8.04911 17.1377 8.95097 17.1377 9.50178 16.5873L10.661 15.4277C10.9364 15.1525 11.4806 14.9272 11.8702 14.9272H13.5103C14.2899 14.9272 14.927 14.2893 14.927 13.5103V11.8708C14.927 11.4806 15.1527 10.9368 15.4277 10.6618L16.5871 9.5026C17.1375 8.95092 17.1375 8.04902 16.5871 7.49821ZM7.37545 11.6877L4.24973 8.5616L5.25148 7.56005L7.3757 9.68426L11.7484 5.31261L12.7499 6.31416L7.37545 11.6877Z" fill="#FDBC33"/>
                                </g>`;
                            });
                            $('.success-message').html("Code added incorrectly. Pro feature disabled. Please review and correct.").css({ 'display': 'block', 'color': 'red' });
                        }
                    }else{
                        $('.success-message').html("Please select valid page/post.").css({ 'display': 'block', 'color': 'red' });            
                    }
                },
                error: function (xhr, status, error) {
                    $('.loader').css('display', 'none');
                }
            });
        } else {
            $('.success-message').html("Please select page/post.").css({ 'display': 'block', 'color': 'red' });
        }
    });
});

