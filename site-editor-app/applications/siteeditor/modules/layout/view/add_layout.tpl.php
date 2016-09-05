<fieldset class="row_setting_box">
    <legend><?php echo __("Layouts Manager","site-editor");?></legend>
    <div class="row_settings">
        <div class="row_setting_inner">

            <!--<div id="sed-app-control-layouts_manager" class="clearfix sed-container-control-element">
                <div ng-app="layoutsManager" ng-controller="layoutCtrl">

                    <div class="sed-layout-lists">
                        <ul>
                            <li ng-repeat="layoutTitle in layouts">

                                <div class="sed-view-mode">
                                    <span ng-click="editItem($index)">{{layoutTitle}} </span>
                                    <span class="fa fa-delete" ng-click="removeItem($index)">   remove  </span>
                                    <span class="fa fa-edit" ng-click="editItem($index)"> edit</span>
                                </div>

                                <div class="sed-edit-mode">

                                </div>

                            </li>
                        </ul>
                    </div>

                    <div class="sed-layout-edit" ng-class="editMode">
                        <input ng-model="editLayout">
                        <button ng-click="saveItem()">Save</button>
                        <span ng-click="closeEditMode()">   x </span>
                    </div>

                    <div class="sed-add-layout">
                        <input ng-model="addLayout">
                        <button ng-click="addItem()">Add</button>
                    </div>

                    <div class="sed-layout-error-box sed-error">
                        <p>{{errortext}}</p>
                    </div>

                </div>
            </div>-->
            <div ng-app="myShoppingList" ng-controller="myCtrl">
                <ul>
                    <li ng-repeat="x in products">
                        <div>
                            <span ng-click="editItem($index)">{{x}} </span>
                            <span ng-click="removeItem($index)">   remove  </span>
                            <span ng-click="editItem($index)"> edit</span>
                        </div>

                    </li>
                </ul>

                <div ng-class="editMode">
                    <input ng-model="editMe">
                    <button ng-click="saveItem()">Save</button>
                    <span ng-click="disableEditMode()">   x </span>
                </div>

                <input class="hide" ng-model="editIndex">

                <input type="text" ng-model="addMe">
                <button ng-click="addItem()">Add</button>
                <p>{{errortext}}</p>

                <p ng-bind="myText.user"></p>

            </div>

            <p>Try to add the same item twice, and you will get an error message.</p>
        </div>
    </div>
</fieldset>