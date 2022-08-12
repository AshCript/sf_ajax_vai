const getProduct = (id) => {
  var temp = null
  $.ajax({
    async: false,
    method: 'GET',
    url: 'http://localhost:8000/api/produit/' + id,
    dataType: 'json',
    success: function(data){
      temp = data
    }
  })
  return temp
}
const getProductMarks = () => {
  var temp = null
  $.ajax({
    async: false,
    method: "GET",
    url: "http://localhost:8000/api/productMarks",
    dataType: 'json',
    success: function (data){
      temp = data;
    }
  })
  return temp
}
const getCategoryProduits = () => {
  var temp = null
    $.ajax({
      async: false,
      method: "GET",
      url: "http://localhost:8000/api/categoryProduits",
      dataType: 'json',
      success: function (data) {
        temp = data
      }
    })
    return temp
}
const getAllProducts = () => {
  var temp = null
    $.ajax({
      async: false,
      method: "GET",
      url: "http://localhost:8000/api/produits",
      dataType: 'json',
      success: function (data) {
        temp = data
      }
    })
    return temp
}


// It returns the ID at index 0 and the category name at the index 1
const getCategoryProduct = (id) => {
  var temp = null
  $.ajax({
    async: false,
    method: 'GET',
    url: 'http://localhost:8000/api/categoryProduit/' + id,
    dataType: 'json',
    success: function(data){
      temp = data
    }
  })
  return temp
}

const showProduits = () => {
  $.ajax({
    method: "GET",
    url: "http://localhost:8000/api/produits",
    dataType: "json"
  }).then(function(produits){
    var catProd = getCategoryProduits()
    var marques = getProductMarks()

    var produit = "<div class='slowmo-show'>"
    produit += "<div class='marques'>"
    for(var marque of marques)
      produit += "<a class='ui label  black mini'>" + marque + "</a>"
    produit += "</div>"
    produit += "<table class='ui inverted black collapsing table'>"
    produit += "<thead>"
    produit += "<tr>"
    produit += "<th>ID</th>"
    produit += "<th>Catégorie</th>"
    produit += "<th>Marque</th>"
    produit += "<th>Modèle</th>"
    produit += "<th>Prix</th>"
    produit += "<th>Prix préc.</th>"
    produit += "<th>Dispo.</th>"
    produit += "<th>"
    produit += "<button class='ui primary inverted circular button' onClick = showProduitForm()>Ajouter un nouveau produit</button>"
    produit += "</th>"
    produit += "</tr>"
    produit += "</thead>"
    produit += "<tbody id='listProd'>"

    for(var p of produits){
      produit += "<tr id='prod" + p.id + "'>"
      produit += "<td>" + p.id + "</td>"

      for(var cp of catProd){
        if(cp.id_produit == p.id){
          produit += "<td>" + cp.nom_category +"</td>"
          break
        }
      }

      produit += "<td>" + p.marque + "</td>"
      produit += "<td>" + p.model + "</td>"
      produit += "<td>" + p.prix + "</td>"
      produit += "<td>" + p.prevPrix + "</td>"
      produit += "<td id='prodDispo" + p.id + "'>"
      produit += "<button style='font-weight: normal; cursor: pointer' onClick='switchDispoProd(\"" + p.id + "\")' class='ui "
      
      if(p.dispo)
        produit += "teal label'>Oui"
      else
        produit += "red label'>Non"
      produit += "</button>"
      produit += "</td>"
      produit += "<td style='text-align: center'>"
      produit += "<i class='file alternate outline circular large yellow inverted icon' onClick='showDetailProduct(" + p.id + ")' style='cursor: pointer;'></i>"
      produit += "<i class='edit outline circular large teal inverted icon' onClick='editProduct(" + p.id + ")' style='cursor: pointer;'></i>"
      produit += "<i class='times circular large red inverted icon' onClick='showDeleteProductModal(" + p.id + ", \"" + p.marque + "\", \"" + p.model + "\")' style='cursor: pointer;'></i>"
      produit += "</td>" 
      produit += "</tr>"
    }

    produit += "</tbody>"
    produit += "<tfoot>"
    produit += "<tr>"
    produit += "<th></th>"
    produit += "<th><div class='ui black inverted circular disabled button' id='totalProduit'>Total: " + produits.length + "</div></th>"
    produit += "<th> </th>"
    produit += "</tr>"
    produit += "</tfoot>"
    produit += "</table>"
    produit += "</div>"

    $("#container").html(produit);
    inactiveButton();
    $("#produitsButton").css(
      "background-color", "rgba(150, 150, 150, 0.2)"
    )
  });
}

const addProduct = () => {
  var id_categorie_produit = $("#id_categorie_produit").val()
  var marque_produit = $("#marque_produit").val()
  var model_produit = $("#model_produit").val()
  var desc_produit = $("#desc_produit").val()
  var prix_produit = $("#prix_produit").val()
  var dispo_produit = $("#dispo_produit").val($("#dispo_produit").prop("checked"))
  dispo_produit = dispo_produit.val() == "true" ? true : false

  $.ajax({
    async: false,
    method: 'POST',
    url: 'http://localhost:8000/api/addProduct',
    dataType: 'json',
    data: JSON.stringify({
      "id_categorie_produit": id_categorie_produit,
      "marque_produit": marque_produit,
      "model_produit": model_produit,
      "desc_produit": desc_produit,
      "prix_produit": prix_produit,
      "dispo_produit": dispo_produit
    }),
    success : function(p){
      var categorie = function(){
        var temp = null
        $.ajax({
          async: false,
          method: 'GET',
          url: 'http://localhost:8000/api/categorie/' + id_categorie_produit,
          dataType: 'json',
          success: function(data){
            temp = data
          }
        })
        return temp
      }()
      var produit = "<tr id='prod" + p.id + "'>"
      produit += "<td>" + p.id + "</td>"
      produit += "<td>" + categorie.nom + "</td>"
      produit += "<td>" + p.marque + "</td>"
      produit += "<td>" + p.model + "</td>"
      produit += "<td>" + p.prix + "</td>"
      produit += "<td>" + p.prevPrix + "</td>"
      produit += "<td id='prodDispo" + p.id + "'>"
      produit += "<button style='font-weight: normal; cursor: pointer' onClick='switchDispoProd(\"" + p.id + "\")' class='ui "
      
      if(p.dispo)
        produit += "teal label'>Oui"
      else
        produit += "red label'>Non"
      produit += "</button>"
      produit += "</td>"
      produit += "<td style='text-align: center'>"
      produit += "<i class='file alternate outline circular large yellow inverted icon' onClick='showDetailProduct(" + p.id + ")' style='cursor: pointer;'></i>"
      produit += "<i class='edit outline circular large teal inverted icon' onClick='editProduct(" + p.id + ")' style='cursor: pointer;'></i>"
      produit += "<i class='times circular large red inverted icon' onClick='showDeleteProductModal(" + p.id + ", \"" + p.marque + "\", \"" + p.model + "\")' style='cursor: pointer;'></i>"
      produit += "</td>" 
      produit += "</tr>"

      $("#listProd").append(produit)
      $("#totalProduit").html("Total: " + getAllProducts().length)
      hideModal()
      var notif = "<div class='as-notif-banner'>"
      notif += "<div class='as-notif-icon'>"
      notif += "<div class='as-success-notif'>"
      notif += "<div class='as-circle'></div>"
      notif += "<div class='as-short-bar'></div>"
      notif += "<div class='as-long-bar'></div>"
      notif += "</div>"
      notif += "<div class='as-notif-message'>"
      notif += p.marque + " " + p.model + " ajouté avec succès!"
      notif += "</div></div></div>"
      $("#notifContainer").html(notif)
    }
  })
}

const editProduct = (id) => {
  var produit = getProduct(id)
  showProduitForm(produit)
}

const updateProduct = (id) => {
  var id_categorie_produit = $("#id_categorie_produit").val()
  var marque_produit = $("#marque_produit").val()
  var model_produit = $("#model_produit").val()
  var desc_produit = $("#desc_produit").val()
  var prix_produit = $("#prix_produit").val()
  var dispo_produit = $("#dispo_produit").val($("#dispo_produit").prop("checked"))
  dispo_produit = dispo_produit.val() == "true" ? true : false
  $.ajax({
    async: false,
    method: 'PUT',
    url: 'http://localhost:8000/api/updateProduct/' + id,
    dataType: 'json',
    data: JSON.stringify({
      'id_categorie': id_categorie_produit,
      'marque': marque_produit,
      'model': model_produit,
      'description': desc_produit,
      'prix': prix_produit,
      'dispo': dispo_produit
    }),
    success: function(p){
      var catProd = getCategoryProduits()
      var produit = "<td>" + p.id + "</td>"

      for(var cp of catProd){
        if(cp.id_produit == p.id){
          produit += "<td>" + cp.nom_category +"</td>"
          break
        }
      }

      produit += "<td>" + p.marque + "</td>"
      produit += "<td>" + p.model + "</td>"
      produit += "<td>" + p.prix + "</td>"
      produit += "<td>" + p.prevPrix + "</td>"
      produit += "<td id='prodDispo" + p.id + "'>"
      produit += "<button style='font-weight: normal; cursor: pointer' onClick='switchDispoProd(\"" + p.id + "\")' class='ui "
      
      if(p.dispo)
        produit += "teal label'>Oui"
      else
        produit += "red label'>Non"
      produit += "</button>"
      produit += "</td>"
      produit += "<td style='text-align: center'>"
      produit += "<i class='file alternate outline circular large yellow inverted icon' onClick='showDetailProduct(" + p.id + ")' style='cursor: pointer;'></i>"
      produit += "<i class='edit outline circular large teal inverted icon' onClick='editProduct(" + p.id + ")' style='cursor: pointer;'></i>"
      produit += "<i class='times circular large red inverted icon' onClick='showDeleteProductModal(" + p.id + ", \"" + p.marque + "\", \"" + p.model + "\")' style='cursor: pointer;'></i>"
      produit += "</td>" 
      produit += "</tr>"

      $("#prod" + p.id).html(produit)
      hideModal()
      var notif = "<div class='as-notif-banner'>"
      notif += "<div class='as-notif-icon'>"
      notif += "<div class='as-success-notif'>"
      notif += "<div class='as-circle'></div>"
      notif += "<div class='as-short-bar'></div>"
      notif += "<div class='as-long-bar'></div>"
      notif += "</div>"
      notif += "<div class='as-notif-message'>"
      notif += p.marque + " " + p.model + " modifié avec succès!"
      notif += "</div></div></div>"
      $("#notifContainer").html(notif)
    }
  })

}

const showDetailProduct = (id) => {
  var p = getProduct(id)

  var produit = "<div class='ui raised very padded inverted text container segment slowmo-show'>"
  produit += "<h2 class='ui header'>"
  produit += p.marque + " " + p.model
  produit += "<span class='ui tag teal label label-cat'>" + getCategoryProduct(p.id)[1] + "</span>"
  produit += "</h2>"
  produit += "<p>"
  produit += "<span class='price-title'>Prix : </span>"
  produit += "<span class='actual-price'>" + p.prix + " Ar.</span>"
  if(p.prix != p.prevPrix)
    produit += "<span class='old-price'>" + p.prevPrix + " Ar.</span>"
  produit += "</p>"
  produit += "<h3 class='detail-description'>"
  produit += "<u>Déscription</u>"
  produit += "<br/>" + p.description
  produit += "</h3>"
  produit += "<h3>Disponibilité : "
  if(p.dispo)
    produit += "<span class='actual-price'>disponible</span>"
  else
    produit += "<span class='actual-price' style='background-color: rgb(196, 75, 75);'>non disponible</span>"
  produit += "</h3>"
  produit += "<h4>Date de création : " + p.createdAt + "</h4>"
  produit += "<h4>Date de dernière modification : " + p.updatedAt + "</h4>"
  produit += "<div class='button-container'>"
  produit += "<button class='ui teal button' onClick='editProduct(" + p.id + ")'>Modifier</button>"
  produit += "<button class='ui red button' onClick='showDeleteProductModal(" + p.id + ", \"" + p.marque + "\", \"" + p.model + "\")'>Supprimer</button>"
  produit += "</div>"
  produit += "<div class='button-back'>"
  produit += "<div onClick='hideModal()' style='cursor: pointer'>"
  produit += "<i class='angle left inverted large circular teal icon'></i>"
  produit += "</div>"
  produit += "</div>"
  produit += "</div>"

  $("#modal").fadeIn("fast")
  $("#modal").html(produit)
  $("#modal").css('top', '25%')
  $("#curtain").css('display', 'block')
}

const showProduitForm = (p = null) => {
  var $categories = function(){
    var temp = null
    $.ajax({
      async: false,
      type: 'GET',
      url: 'http://localhost:8000/api/categories',
      dataType: 'json',
      success: function(data){
        temp = data
      }
    })
    return temp
  }()

  var produitForm = "<div class='ui raised very padded inverted text container segment slowmo-show'>"
  produitForm += "<h2 class='ui centered text'>"
  produitForm += "</h2>"
  produitForm += "<form method='post' class='ui form'>"
  produitForm += "<div class='three fields'>"
  if(p !== null){
    var categoryProduct = getCategoryProduct(p.id)[0]
    produitForm += "<div class='field'>Catégorie"
    produitForm += "<select id='id_categorie_produit' name='categorie'>"
    for($category of $categories){
      produitForm += "<option value='" + $category.id + "' "
      if($category.id == categoryProduct)
        produitForm += " selected "
      produitForm += ">" + $category.nom + "</option>"
    }
    produitForm += "</select>"
    produitForm += "</div>"
    produitForm += "<div class='field'>Marque"
    produitForm += "<input type='text' id='marque_produit' value='" + p.marque + "' name='marque' placeholder='Marque'/>"
    produitForm += "</div>"
    produitForm += "<div class='field'>Model"
    produitForm += "<input type='text' id='model_produit' value='" + p.model + "' name='model' placeholder='Model'/>"
    produitForm += "</div>"
    produitForm += "</div>"
    produitForm += "<div class='field'>"
    produitForm += "<textarea id='desc_produit' style='width: 100%; height: 100px;' name='description'>" + p.description + "</textarea>"
    produitForm += "</div>"
    produitForm += "<div class='four fields'>"
    produitForm += "<div class='field'>Prix"
    produitForm += "<input id='prix_produit' value='" + p.prix + "' type='number' name='prix'/>"
    produitForm += "</div>"
    produitForm += "</div>"
                  
    produitForm += "<div class='field'>"
    produitForm += "<input id='dispo_produit' type='checkbox' " + (p.dispo? "checked":"") + " name='dispo'/> Disponibilité"
    produitForm += "</div>"
    produitForm += "<div class='button-container' style='bottom: -30px;'>"
    produitForm += "<input class='ui teal button' type='button' onClick=\"updateProduct('" + p.id + "')\" value='Enregistrer'>"
    produitForm += "</div>"
  }else{
    produitForm += "<div class='field'>Catégorie"
    produitForm += "<select id='id_categorie_produit' name='categorie'>"
    for($category of $categories)
      produitForm += "<option value='" + $category.id + "'>" + $category.nom + "</option>"
    produitForm += "</select>"
    produitForm += "</div>"
    produitForm += "<div class='field'>Marque"
    produitForm += "<input type='text' id='marque_produit' name='marque' placeholder='Marque'/>"
    produitForm += "</div>"
    produitForm += "<div class='field'>Model"
    produitForm += "<input type='text' id='model_produit' name='model' placeholder='Model'/>"
    produitForm += "</div>"
    produitForm += "</div>"
    produitForm += "<div class='field'>"
    produitForm += "<textarea id='desc_produit' style='width: 100%; height: 100px;' name='description'/>"
    produitForm += "</div>"
    produitForm += "<div class='four fields'>"
    produitForm += "<div class='field'>Prix"
    produitForm += "<input id='prix_produit' type='number' name='prix'/>"
    produitForm += "</div>"
    produitForm += "</div>"
                  
    produitForm += "<div class='field'>"
    produitForm += "<input id='dispo_produit' type='checkbox' name='dispo'/> Disponibilité"
    produitForm += "</div>"
    produitForm += "<div class='button-container' style='bottom: -30px;'>"
    produitForm += "<input class='ui teal button' type='button' onClick=\"addProduct()\" value='Enregistrer'>"
    produitForm += "</div>"
  }
  produitForm += "</form>"
  produitForm += "<div class='button-back'>"
  produitForm += "<div onClick='hideModal()' style='cursor: pointer'>"
  produitForm += "<i class='angle left inverted large circular teal icon'></i>"
  produitForm += "</div>"
  produitForm += "</div>"
  produitForm += "</div>"

  $("#modal").fadeIn("fast")
  $("#modal").html(produitForm)
  $("#modal").css('top', '25%')
  $("#curtain").css('display', 'block')
}

const  showDeleteProductModal = (id, marque, model) =>{
  var wiwiMirror = "<div id='wiwi-mirror'>"
  wiwiMirror+="<div class='wiwi-ban-container'>"
  wiwiMirror+="<div class='wiwi-ban-message'>"
  wiwiMirror+="Voulez-vous vraiment supprimer l'article " + marque + " " + model + "? <br/>Attention, cela risque de supprimer toutes les actions liées à cet article!"
  wiwiMirror+="</div>"
  wiwiMirror+="<div class='wiwi-ban-btn-confirm'>"
  wiwiMirror+="<button class='wiwi-btn-deny' onClick = 'deleteProduct(" + id + ")'>"
  wiwiMirror+="Oui"
  wiwiMirror+="</button>"
  wiwiMirror+="<button class='wiwi-btn-confirm' onclick='hideWiwiBan()'>"
  wiwiMirror+="Non"
  wiwiMirror+="</button>"
  wiwiMirror+="</div>"
  wiwiMirror+="</div>"
  wiwiMirror+="</div>"
  document.getElementById("messageBox").style.zIndex = 1000
  document.getElementById("messageBox").innerHTML = wiwiMirror
 
  wiwiMirror.style.zIndex = 0
  setInterval(() =>{
    wiwiMirror.style.opacity += .2
    if(wiwiMirror.style.opacity == 1)
      clearInterval()
  }, 250)
}
const deleteProduct = (id) => {
  $.ajax({
    async: false,
    method: 'DELETE',
    url: 'http://localhost:8000/api/deleteProduct/' + id,
    dataType: 'json',
    success: function(data){
      $("#prod" + id).remove()
      $("#totalProduit").html("Total: " + getAllProducts().length)
      hideWiwiBan()
      var notif = "<div class='as-notif-banner'>"
      notif += "<div class='as-notif-icon'>"
      notif += "<div class='as-success-notif'>"
      notif += "<div class='as-circle'></div>"
      notif += "<div class='as-short-bar'></div>"
      notif += "<div class='as-long-bar'></div>"
      notif += "</div>"
      notif += "<div class='as-notif-message'>"
      notif += data[0] + " " + data[1] + " supprimé avec succès!"
      notif += "</div></div></div>"
      $("#notifContainer").html(notif)
    }
  })
}

// It switches the disponibility of a specific product on the list.
const switchDispoProd = (id) => {
  var p = getProduct(id)
  $.ajax({
    async: false,
    method: 'POST',
    url: 'http://localhost:8000/api/updateDispoProduct',
    dataType: 'json',
    data: JSON.stringify({"id": id}),
    success: function(dispo){
      var content = "<button style='font-weight: normal; cursor: pointer' onClick='switchDispoProd(\"" + id + "\")' class='ui "
      var message = ""
      if(dispo){
        content += "teal label'>Oui"
        message = "\"" + p.marque + " " + p.model + "\" est désormais disponible"
      }else{
        content += "red label'>Non"
        message = "\"" + p.marque + " " + p.model + "\" non disponible"
      }
      content += "</button>"
      $('#prodDispo' + id).html(content)
      var notif = "<div class='as-notif-banner'>"
      notif += "<div class='as-notif-icon'>"
      notif += "<div class='as-success-notif'>"
      notif += "<div class='as-circle'></div>"
      notif += "<div class='as-short-bar'></div>"
      notif += "<div class='as-long-bar'></div>"
      notif += "</div>"
      notif += "<div class='as-notif-message'>"
      notif += message
      notif += "</div></div></div>"
      $("#notifContainer").html(notif)
    }
  })
}