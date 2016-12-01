var quickAction = {};

quickAction.MenuOption = function(title, action)
{
    this.title = title;
    this.action = action;
}

quickAction.MenuOption.prototype.getTitle = function()
{
    return this.title;
}

quickAction.MenuOption.prototype.getAction = function()
{
    return this.action;
}


quickAction.MenuOption.prototype.getHtml = function()
{
    return '<a href="javascript:void(0);" onclick="' + this.getAction() + ' closeQuickActionMenu();">' + this.getTitle() + '</a><br />';
}

quickAction.LinkMenuOption = function(title, action, option)
{
    quickAction.MenuOption.call(this, title, action);
    this.option = option;
}

quickAction.LinkMenuOption.prototype = Object.create(quickAction.MenuOption.prototype);

quickAction.LinkMenuOption.prototype.getOption = function()
{
    return this.option;
}

quickAction.LinkMenuOption.prototype.getHtml = function()
{
    var message = "'Are you sure?'";
    var result;
    switch(this.getOption())
    {
        case 0:
            var itemAction = "'" + this.getAction() + "'";
            result = '<a href=# onclick="showPopWin(' + itemAction + ', 750, 540, null); return false;">' + this.getTitle() + '</a><br />';
            break;
        case 1:
        default:
            result = '<a href="' + this.getAction() + '" onclick="return confirm(' + message + ')">' + this.getTitle() + '</a><br />';
            break;
    }
    return result;
}


quickAction.DefaultMenu = function(menuDataItemType, menuDataItemId, menuX, menuY)
{
    this.element = document.getElementById('singleQuickActionMenu');
    this.menuDataItemType = menuDataItemType;
    this.menuDataItemId = menuDataItemId;
    this.menuX = menuX;
    this.menuY = menuY;
}

quickAction.DefaultMenu.prototype.getOptions = function()
{
    return [
        new quickAction.MenuOption('Add To List', 'showQuickActionAddToList();')
    ];
}

quickAction.DefaultMenu.prototype.toggle = function()
{
    if (this.element.style.display == 'block')
    {
        closeQuickActionMenu();
    } else {
        this.element.style.display = 'block';
        this.element.style.left = this.menuX + 'px';
        this.element.style.top = this.menuY + 'px';
        this.element.innerHTML = '';
        var options = this.getOptions();
        for (var i = 0; i < options.length; ++i)
        {
            console.log(options[i].getHtml());
            this.element.innerHTML += options[i].getHtml();
        }
    }
}

quickAction.DefaultMenu.prototype.closeQuickActionMenu = function()
{
    var singleQuickActionMenu = document.getElementById('singleQuickActionMenu');
    singleQuickActionMenu.style.display = 'none';
}


quickAction.CandidateMenu = function(menuDataItemType, menuDataItemId, menuX, menuY)
{
    quickAction.DefaultMenu.call(this, menuDataItemType, menuDataItemId, menuX, menuY);
}

quickAction.CandidateMenu.prototype = Object.create(quickAction.DefaultMenu.prototype);

quickAction.CandidateMenu.prototype.getOptions = function()
{
    return [
        new quickAction.MenuOption('Add To List', 'showQuickActionAddToList();'),
        new quickAction.MenuOption('Add To Pipeline', 'showQuickActionAddToPipeline();')
    ];
}

quickAction.DuplicateCandidateMenu = function(menuDataItemType, menuDataItemId, menuX, menuY, mergeUrl, removeUrl)
{
    quickAction.DefaultMenu.call(this, menuDataItemType, menuDataItemId, menuX, menuY);
    this.mergeUrl = mergeUrl;
    this.removeUrl = removeUrl;
}

quickAction.DuplicateCandidateMenu.prototype = Object.create(quickAction.DefaultMenu.prototype);

quickAction.DuplicateCandidateMenu.prototype.getOptions = function()
{
    console.log(this.mergeUrl);
    console.log(this.removeUrl);
    return [
        new quickAction.LinkMenuOption('Merge', this.urlDecode(this.mergeUrl), 0),
        new quickAction.LinkMenuOption('Remove duplicity warning', this.urlDecode(this.removeUrl), 1)
    ];
}

quickAction.DuplicateCandidateMenu.prototype.urlDecode = function(url)
{
    return decodeURIComponent(url.replace(/\+/g, ' '));
}

/* Creates and displays a popup menu for an individual data item on the page to do some simple action to. */
function showHideSingleQuickActionMenu(menu)
{
    menu.toggle();
}

/* Shows a popup for adding a item to a list. */
function showQuickActionAddToList()
{
    /* Create a popup window for adding this data item type to a list (content loaded from server) */
    showPopWin(CATSIndexName + '?m=lists&a=quickActionAddToListModal&dataItemType='+_singleQuickActionMenuDataItemType+'&dataItemID='+_singleQuickActionMenuDataItemID, 450, 350, null);
}

/* Shows a popup for adding a item to a list. */
function showQuickActionAddToPipeline()
{
    /* Create a popup window for adding this candidate to the pipeline */
    showPopWin(CATSIndexName + '?m=candidates&a=considerForJobSearch&candidateID='+_singleQuickActionMenuDataItemID, 750, 390, null);
}

// TODO: Fix this static method that "knows" about singleQuickActionMenu, it should be part of the Menu class
function closeQuickActionMenu()
{
    var singleQuickActionMenu = document.getElementById('singleQuickActionMenu');
    singleQuickActionMenu.style.display = 'none';
}

function mergeCandidates()
{
    
}

