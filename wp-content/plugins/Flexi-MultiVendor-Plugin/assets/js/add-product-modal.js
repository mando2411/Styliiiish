








jQuery(function($){










if(window.wfAddModalLoaded){return;}
    
window.currentProductId = 0;
let pendingAttrs = null;






/* ===========================
   IMAGE UPLOAD SYSTEM
=========================== */
/* ===========================
   OPEN IMAGE MODAL FROM BUTTON
=========================== */
$(document).on("click", ".styliiiish-upload-btn", function (e) {

    e.preventDefault();
    lockScroll();

    // 👇 رجّع الوضع لمنتج
    currentImageContext = "product";
    currentTargetID = null;

    // Priority: JS → HTML
    let pid = window.currentProductId;

    if (!pid) {
        pid = $(this).data("id");
    }

    currentImageProd = pid;

    console.log("FINAL PRODUCT ID:", currentImageProd);
    console.log("CTX:", currentImageContext);

    if (!currentImageProd) {
        Swal.fire(
          "Wait",
          "Product is not ready yet",
          "info"
        );
        return;
    }

    sendRequest(
        "styliiiish_get_images",
        { product_id: currentImageProd },
        function (response) {

            $("#styliiiish-images-list").html(response.data.html);

            $("#styliiiishImageModal").css("display", "flex");
        }
    );
});










$(document).on('click','#addProductModal',function(e){

   if(e.target === this){

      // اقفل فقط لو ضغطت على الخلفية
      closeBuilder();
      

   }

});


$(document).on('click','.wf-ui-modal-box',function(e){
   e.stopPropagation();
});



$(document).on('click','#closeAddProductModal',function(){

   closeBuilder();

});


function closeBuilder(){

   $('#addProductModal').fadeOut();

   // 🔥 نظّف كل حاجة
   resetBuilder();

   // 🔓 رجّع السكرول
   unlockScroll();

   $('#saveProductBtn')
     .prop('disabled', false)
     .text('Save');
}



/* ==========================
   SUBMIT
========================== */

$(document).on('submit','#addProductForm',function(e){

 e.preventDefault();

 let form = new FormData(this);

 form.append('action','styliiiish_add_product');
 form.append('nonce', wfModal.nonce); // مهم
 form.append('product_id', window.currentProductId);

 console.log('SAVE NONCE:', wfModal.nonce);
 console.log('SAVE PID:', window.currentProductId);

 $.ajax({

   url: wfModal.ajax,
   type:'POST',
   data:form,

   processData:false,
   contentType:false,

   success:function(res){

     console.log('SAVE RESPONSE:',res);

     if(res.success){

        Swal.fire('Saved','Done','success');

        // If the manage-products list exists, reload it and close the modal
        // only after the list's AJAX completes so the UI reflects the change.
        if (typeof loadManageProductsPage === 'function' && $('#styliiiish-manage-products-content').length) {

           $(document).one('ajaxStop', function(){
              closeBuilder();
           });

           try{
              // preserve current page if available
              var page = (window.currentFilters && window.currentFilters.page) ? window.currentFilters.page : 1;
              loadManageProductsPage(page);
           }catch(e){
              loadManageProductsPage(1);
           }

        } else {
           // fallback: close immediately
           closeBuilder();
        }

     }else{

        Swal.fire('Error',res.data,'error');
     }

   },

   error:function(xhr){

     console.log('STATUS:',xhr.status);
     console.log('RESPONSE:',xhr.responseText);

   }

 });

});


/* ==========================
   LIVE PREVIEW
========================== */

$(document).on('input','#fTitle',function(){

   $('#prevTitle').text(this.value || 'Product Name');

});


$(document).on('input','#fDesc',function(){

   $('#prevDesc').text(this.value || 'Product description...');

});


/* ==========================
   PRICE PREVIEW
========================== */

$(document).on('input','#fRegularPrice, #fSalePrice',function(){

   let reg  = parseFloat($('#fRegularPrice').val());
   let sale = parseFloat($('#fSalePrice').val());

   // لو مفيش سعر أساسًا
   if(!reg || reg <= 0){

      $('#prevRegular')
        .removeClass('discount')
        .text('—');

      $('#prevSale').hide();
      $('#prevDiscount').text('');
      return;
   }

   if(sale > 0 && sale < reg){

      let percent = Math.round(100 - (sale/reg*100));

      $('#prevRegular')
        .addClass('discount')
        .text(reg+' EGP');

      $('#prevSale')
        .text(sale+' EGP')
        .fadeIn(120);

      $('#prevDiscount')
        .text('-'+percent+'%');

   }else{

      $('#prevRegular')
        .removeClass('discount')
        .text(reg+' EGP');

      $('#prevSale').fadeOut(120);
      $('#prevDiscount').text('');
   }
});





$(document).on('blur', '#fSalePrice', function(){

   let reg  = parseFloat($('#fRegularPrice').val());
   let sale = parseFloat(this.value);

   if(!reg || !sale) return;

   if(sale >= reg){

      this.value = '';

      Swal.fire(
         'Invalid price',
         'Sale must be lower than regular',
         'warning'
      );

      $(this).focus();
   }
});




/* ==========================
   RESET
========================== */

function resetBuilder(){

   let pid = window.currentProductId;

   $('#addProductForm')[0].reset();

   window.currentProductId = pid;
   $('#currentProductId').val(pid);

   // 🔥 مهم
   pendingAttrs = null;

   $('#previewImg').hide();
   $('#noImg').show();

   $('#prevTitle').text('Product Name');
   $('#prevDesc').text('Product description...');

   $('#prevCats').html('');
   $('#prevAttrs').html('');

   $('#prevRegular')
     .removeClass('discount')
     .text('0 EGP');

   $('#prevSale').hide();
}



/* ===========================
   LOAD ATTRIBUTES ON CAT CHANGE
=========================== */

/* ===========================
   CATEGORY CHANGE
=========================== */

$(document).on('change','#fCats',function(){

 let cat = $(this).val();

 /* ---------- PREVIEW ---------- */

 let html='';

 $('#fCats option:selected').each(function(){

   html += '<span>'+$(this).text()+'</span>';

 });

 $('#prevCats').html(html);


 /* ---------- ATTR AJAX ---------- */

 if(!cat){

   $('.attr-box').html('<p>Select category first</p>');
   return;
 }

 $('.attr-box').html('<p>Loading...</p>');


 $.post(wfModal.ajax,{

   action : 'styliiiish_get_attributes',
   nonce  : wfModal.nonce,
   cat_id : cat

 },function(res){

   console.log(res); // ← مهم جدًا للتجربة

   if(res.success){

     buildAttrs(res.data);

   }else{

     $('.attr-box').html('<p>No attributes</p>');
   }

 });

});



function buildAttrs(data){

   let html = '';

   if(!Array.isArray(data) || !data.length){

      html = '<p>No attributes for this category</p>';

   }else{

      data.forEach(function(attr){

         html += `
         <div class="attr-group">

            <div class="attr-title">
               ${attr.label}
            </div>

            <div class="attr-list">
         `;

         attr.options.forEach(function(opt){

            html += `
            <label class="attr-item">
               <input type="radio"
                      name="attrs[${attr.taxonomy}]"
                      value="${opt.value}">
               <span>${opt.label}</span>
            </label>
            `;

         });

         html += `
            </div>
         </div>
         `;
      });

   }

   $('.attr-box').html(html);

   /* APPLY PENDING ATTRS */
   if(pendingAttrs && typeof pendingAttrs === "object"){

      requestAnimationFrame(function(){

         for(let tax in pendingAttrs){

            $(`input[name="attrs[${tax}]"][value="${pendingAttrs[tax]}"]`)
              .prop("checked", true)
              .trigger("change");
         }

         pendingAttrs = null;

      });
   }
}




/* ===========================
   ATTR PREVIEW
=========================== */

$(document).on('change','.attr-box input',function(){

 let html='';

 $('.attr-group').each(function(){

   let checked = $(this).find('input:checked');

   if(checked.length){

     html += '<span class="preview-attr">'+checked.parent().text().trim()+'</span>';


   }

 });

 $('#prevAttrs').html(html);

});









/* ==========================
   EDIT PRODUCT
========================== */

$(document).on('click','.btn-edit-product',function(e){

    lockScroll();
   e.preventDefault();

   let pid = $(this).data('id');

   if(!pid) return;

   console.log('Edit product:', pid);

   $.post(wfModal.ajax,{

      action     : 'styliiiish_get_product_for_edit',
      nonce      : wfModal.nonce,
      product_id : pid

   },function(res){

      if(!res.success){
         Swal.fire('Error',res.data,'error');
         return;
      }

      let p = res.data;

        
        pendingAttrs = p.attrs || null;


      
      
      // ✅ PREVIEW IMAGE
        if(p.image){
        
           $("#previewImg")
             .attr("src", p.image)
             .show();
        
           $("#noImg").hide();
        
        }else{
        
           $("#previewImg").hide();
           $("#noImg").show();
        }


      // خزّن ID
      window.currentProductId = p.id;
      $('#currentProductId').val(p.id);

      // افتح المودال
      $('#addProductModal').fadeIn();

      // عبّي الفورم
      $('#fTitle').val(p.title);
      $('#fDesc').val(p.desc);
      $('#fRegularPrice').val(p.price);
        $('#fSalePrice').val(p.sale || '');
        
        $('#fRegularPrice').trigger('input').blur();



      // الكاتيجوري
      if(p.cats && p.cats.length){
         $('#fCats').val(p.cats).trigger('change');
      }

      // تحديث المعاينة
      $('#prevTitle').text(p.title);
      $('#prevDesc').text(p.desc);

   });

});










});
