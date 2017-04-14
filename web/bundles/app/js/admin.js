"use strict";

//Activate/Deactivate user
function activateUser(e, uid, mode){

    var ele = $(e);

    $.ajax({
        type: 'POST',
        url: baseUrl+'admin/users/'+uid+'/activate',
        data: {'mode': mode},
        beforeSend: function () {
            //disable button while the AJAX call is underway
            ele.prop('disabled', true);
        },
        success: function(response){
            //If we get a proper response and status code is 1
            if(response.hasOwnProperty('code') && response.code == 1){

                //Replace button with proper text on activate/deactive
                if(mode == 1){
                    ele.replaceWith(
                        '<span onclick="activateUser(this, '+uid+', '+(-1)+')" class="btn btn-sm btn-primary">Deactivate</span>'
                    );
                }else{
                    ele.replaceWith(
                        '<span onclick="activateUser(this, '+uid+', '+(1)+')" class="btn btn-sm btn-primary">Activate</span>'
                    );
                }
            }else{
                alert("Something went wrong!");
            }
        },
        complete: function () {
            ele.prop('disabled', false);
        }
    });

}//activeUser

//Block/Unblock user
function blockUser(e, uid, mode){

    var ele = $(e);

    $.ajax({
        type: 'POST',
        url: baseUrl+'admin/users/'+uid+'/block',
        data: {'mode': mode},
        beforeSend: function () {
            //disable button while the AJAX call is underway
            ele.prop('disabled', true);
        },
        success: function(response){
            //If we get a proper response and status code is 1
            if(response.hasOwnProperty('code') && response.code == 1){

                //Replace button with proper text on Block/Unblock
                if(mode == 1){
                    ele.replaceWith(
                        '<span onclick="blockUser(this, '+uid+', '+(-1)+')" class="btn btn-sm btn-warning">Unblock</span>'
                    );
                }else{
                    ele.replaceWith(
                        '<span onclick="blockUser(this, '+uid+', '+(1)+')" class="btn btn-sm btn-warning">Block</span>'
                    );
                }

            }else{
                alert("Something went wrong!");
            }
        },
        complete: function () {
            ele.prop('disabled', false);
        }
    })

}//blockUser

//Make Admin/Remove as admin
function makeAdmin(e, uid, mode){

    var ele = $(e);

    $.ajax({
        type: 'POST',
        url: baseUrl+'admin/users/'+uid+'/grant-admin',
        data: {'mode': mode},
        beforeSend: function () {
            //disable button while the AJAX call is underway
            ele.prop('disabled', true);
        },
        success: function(response){
            //If we get a proper response and status code is 1
            if(response.hasOwnProperty('code') && response.code == 1){

                //Replace button with proper text
                if(mode == 1){
                    ele.replaceWith(
                        '<span onclick="makeAdmin(this, '+uid+', '+(-1)+')" class="btn btn-sm btn-success">Remove as Admin</span>'
                    );
                }else{
                    ele.replaceWith(
                        '<span onclick="makeAdmin(this, '+uid+', '+(1)+')" class="btn btn-sm btn-success">Make Admin</span>'
                    );
                }

            }else{
                alert("Something went wrong!");
            }
        },
        complete: function () {
            ele.prop('disabled', false);
        }
    })

}//makeAdmin


