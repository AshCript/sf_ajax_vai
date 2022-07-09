const getCategory = (id) => {
  var temp = null
  $.ajax({
    async: false,
    method: 'GET',
    url: 'http://localhost:8000/api/categorie/' + id,
    dataType: 'json',
    success: function(data){
      temp = data
    }
  })
  return temp
}

const showCategories = (categoryPath) => {
  $.ajax({
    method: "GET",
    url: "http://localhost:8000/api/categories",
    dataType: "json"
  }).then(function(cats){
    var countProdCat = function(){
      var tmp = null;
      $.ajax({
        async: false,
        method: "GET",
        url: "http://localhost:8000/api/countProduitsCategory",
        dataType: 'json',
        success: function (data) {
          tmp = data
        }
      })
      return tmp
    }();
    
    var categories = "<table class='ui inverted black collapsing table slowmo-show'>"
    categories += "<thead>"
    categories += "<tr>"
    categories += "<th>ID</th>"
    categories += "<th>Nom</th>"
    categories += "<th>Date de création</th>"
    categories += "<th>Nb. articles</th>"
    categories += "<th>"
    categories += "<button class='ui primary inverted circular button' onClick='showCatForm()'>Ajouter</button>"
    categories += "</th>"
    categories += "</tr>"
    categories += "</thead>"
    categories += "<tbody id='listCat'>"
    for(var c of cats){
      categories += "<tr id='cat" + c.id + "'>"
      categories += "<td>" + c.id + "</td>"
      categories += "<td>" + c.nom + "</td>"
      categories += "<td>" + c.createdAt + "</td>"
      categories += "<td>"
      categories += "<a "

      for(var cpc of countProdCat){
        if(cpc.id_category == c.id){
          categories += "class='ui teal circular inverted button'>" + cpc.count + "</a></td>"
          break
        }
      }
      categories += "<td>"
      categories += "<i class='edit outline circular large teal inverted icon' style='cursor: pointer' onClick='editCategory(" + c.id + ")'></i>"
      categories += "<i class='times circular large red inverted icon' style='cursor: pointer' onClick='showDeleteCategoryModal(" + c.id + ", \"" + c.nom + "\")'></i>"
      categories += "</td>"
      categories += "</tr>"
    }
    categories += "</tbody>"
    categories += "<tfoot>"
    categories += "<tr>"
    categories += "<th></th>"
    categories += "<th><div class='ui black inverted circular disabled button' id='totalCategories'>Total: " + cats.length + "</div></th>"
    categories += "<th> </th>"
    categories += "</tr>"
    categories += "</tfoot>"
    categories += "</table>"
    categories += "</div>"

    $("#container").html(categories);
    inactiveButton();
    $("#categoriesButton").css(
      "background-color", "rgba(150, 150, 150, 0.2)"
    )
  })
}

const addCategory = () => {
  var nomCategorie = $('#nomCategorie').val()
  $.ajax({
    async: false,
    method: 'POST',
    url: 'http://localhost:8000/api/addCategory',
    dataType: 'json',
    data: JSON.stringify({
      'nom': nomCategorie
    }),
    success: function(c){
      var countProdCat = function(){
        var tmp = null;
        $.ajax({
          async: false,
          method: "GET",
          url: "http://localhost:8000/api/countProduitsCategory",
          dataType: 'json',
          success: function (data) {
            tmp = data;
          }
        });
        return tmp;
      }();

      var categorie = "<tr id='cat" + c.id + "'>"
      categorie += "<td>" + c.id + "</td>"
      categorie += "<td>" + c.nom + "</td>"
      categorie += "<td>" + c.createdAt + "</td>"
      categorie += "<td>"
      categorie += "<a "

      for(var cpc of countProdCat){
        if(cpc.id_category == c.id){
          categorie += "class='ui teal circular inverted button'>" + cpc.count + "</a></td>"
          break
        }
      }
      categorie += "<td>"
      categorie += "<i class='edit outline circular large teal inverted icon' style='cursor: pointer' onClick='editCategory(" + c.id + ")'></i>"
      categorie += "<i class='times circular large red inverted icon' style='cursor: pointer' onClick='showDeleteCategoryModal(" + c.id + ", \"" + c.nom + "\")'></i>"
      categorie += "</td>"
      categorie += "</tr>"

      $("#listCat").append(categorie)
      $('#totalCategories').html("Total : " + getCategoryLength())
      hideModal()
      var notif = "<div class='as-notif-banner'>"
      notif += "<div class='as-notif-icon'>"
      notif += "<div class='as-success-notif'>"
      notif += "<div class='as-circle'></div>"
      notif += "<div class='as-short-bar'></div>"
      notif += "<div class='as-long-bar'></div>"
      notif += "</div>"
      notif += "<div class='as-notif-message'>"
      notif += "Catégorie \"" + c.nom + "\" ajouté avec succès!"
      notif += "</div></div></div>"
      $("#notifContainer").html(notif)
    }
  })
}

const getCategoryLength = () => {
  var tmp = null;
  $.ajax({
    async: false,
    method: "GET",
    url: "http://localhost:8000/api/categories",
    dataType: 'json',
    success: function (data) {
      tmp = data.length;
    }
  });
  return tmp;
}

const editCategory = (id) => {
  var categorie = function(){
    var temp = null
    $.ajax({
      async: false,
      method: 'GET',
      url: 'http://localhost:8000/api/categorie/' + id,
      dataType: 'json',
      success: function(data){
        temp = data
      }
    })
    return temp
  }()
  showCatForm(categorie)
}

const updateCategory = (id) => {
  var nomCategorie = $('#nomCategorie').val()
  $.ajax({
    async: false,
    method: 'POST',
    url: 'http://localhost:8000/api/updateCategory/' + id,
    dataType: 'json',
    data: JSON.stringify({
      'id': id,
      'nom': nomCategorie
    }),
    success: function(c){
      var countProdCat = function(){
        var temp = null;
        $.ajax({
          'async': false,
          'method': "GET",
          'url': "http://localhost:8000/api/countProduitsCategory",
          'dataType': 'json',
          'success': function (data) {
            temp = data
          }
        });
        return temp
      }();
      var categorie = "<td>" + c.id + "</td>"
      categorie += "<td>" + c.nom + "</td>"
      categorie += "<td>" + c.createdAt + "</td>"
      categorie += "<td>"
      categorie += "<a "

      for(var cpc of countProdCat){
        if(cpc.id_category == c.id){
          categorie += "class='ui teal circular inverted button'>" + cpc.count + "</a></td>"
          break
        }
      }
      categorie += "<td>"
      categorie += "<i class='edit outline circular large teal inverted icon' style='cursor: pointer' onClick='editCategory(" + c.id + ")'></i>"
      categorie += "<i class='times circular large red inverted icon' style='cursor: pointer' onClick='showDeleteCategoryModal(" + c.id + ", \"" + c.nom + "\")'></i>"
      categorie += "</td>"
      
      $("#cat" + id).html(categorie)
      hideModal()
      var notif = "<div class='as-notif-banner'>"
      notif += "<div class='as-notif-icon'>"
      notif += "<div class='as-success-notif'>"
      notif += "<div class='as-circle'></div>"
      notif += "<div class='as-short-bar'></div>"
      notif += "<div class='as-long-bar'></div>"
      notif += "</div>"
      notif += "<div class='as-notif-message'>"
      notif += "Catégorie \"" + c.nom + "\" modifiée avec succès!"
      notif += "</div></div></div>"
      $("#notifContainer").html(notif)
    }
  })
}

const  showDeleteCategoryModal = (id, nomCategorie) =>{
 var wiwiMirror = "<div id='wiwi-mirror'>"
  wiwiMirror+="<div class='wiwi-ban-container'>"
  wiwiMirror+="<div class='wiwi-ban-message'>"
  wiwiMirror+="Voulez-vous vraiment supprimer la catégorie " + nomCategorie + "? <br/>Attention, cela risque de supprimer tous les articles liés à cette catégorie!"
  wiwiMirror+="</div>"
  wiwiMirror+="<div class='wiwi-ban-btn-confirm'>"
  wiwiMirror+="<button class='wiwi-btn-deny' onClick = 'deleteCategory(" + id + ")'>"
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

const deleteCategory = (id) => {
  var cat = getCategory(id)
  $.ajax({
    async: false,
    method: 'DELETE',
    url: 'http://localhost:8000/api/deleteCategory/' + id,
    dataType: 'json',
    success: function(data){
      $('#cat' + id).remove()
    }
  })
  $('#totalCategories').html("Total : " + getCategoryLength())
  hideWiwiBan()
  var notif = "<div class='as-notif-banner'>"
      notif += "<div class='as-notif-icon'>"
      notif += "<div class='as-success-notif'>"
      notif += "<div class='as-circle'></div>"
      notif += "<div class='as-short-bar'></div>"
      notif += "<div class='as-long-bar'></div>"
      notif += "</div>"
      notif += "<div class='as-notif-message'>"
      notif += "Catégorie \"" + cat.nom + "\" supprimée avec succès!"
      notif += "</div></div></div>"
      $("#notifContainer").html(notif)
}

const showCatForm = (c = null) => {

  var categoryForm = "<div class='ui raised very padded inverted text container segment slowmo-show'>"
  categoryForm += "<h2 class='ui centered text'>"
  if(c !== null){
    categoryForm += "Modification de la catégorie \"" + c.nom + "\""
  }else{
    categoryForm += "Ajout d'une nouvelle catégorie"
  }
  
  categoryForm += "</h2><hr/>"
  categoryForm += "<div class='button-back' style='position: absolute;'>"
  categoryForm += "<div onClick='hideModal()' style='cursor: pointer'>"
  categoryForm += "<i class='angle left inverted large circular teal icon'></i>"
  categoryForm += "</div>"
  categoryForm += "</div>"
  categoryForm += "<form action='' method='post' class='ui form'>"
  categoryForm += "<div class='three fields'>"
  categoryForm += "<div class='field' style='display: inline-block'>Nom de la catégorie"
  categoryForm += "<input type='text' name='categorie'"
  if(c !== null){
    categoryForm += " value = '" +  c.nom + "' "
  }
  categoryForm += "id='nomCategorie'/>"
  categoryForm += "</div>"
  categoryForm += "<div class='button-container' style='display: inline-block; bottom: 0;'>"
  categoryForm += "<input class='ui teal button' type='button'"
  if(c !== null){
    categoryForm += " onClick='updateCategory(" + c.id + ")' " 
  }else{
    categoryForm += " onClick='addCategory()' "
  }
  categoryForm += "value='Enregistrer'>"
  categoryForm += "</div>"
  categoryForm += "</form>"
  categoryForm += "</div>"
  
  $("#modal").fadeIn("fast")
  $("#modal").html(categoryForm)
  $("#modal").css('top', '30%')
  $("#curtain").css('display', 'block')
}
