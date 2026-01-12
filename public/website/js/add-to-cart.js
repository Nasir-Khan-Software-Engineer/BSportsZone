Website.AddToCart = (function(data) {

    let addToCartObject = {
        items: []
    };



   var addProductToCart = function (product) {
        let product = addProductToCart.items.find(item => item.id == product.id && item.variation_id == product.variation_id);
        if(product == null || product == undefined) {
            addToCartObject.items.push({
                id: product.id,
                variation_id: product.variation_id,
                tagline: product.tagline,
                quantity: 1,
                price: product.price,
                name: product.name,
                image: product.image,
                discount_type: product.discount_type,
                discount_value: product.discount_value
            });
        }else{
            product.quantity += 1;
        }
   }

    return {
        updateCartCount: updateCartCount
    };
})(data);