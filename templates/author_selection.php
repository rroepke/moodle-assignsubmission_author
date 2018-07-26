<?php
/**
 * Created by PhpStorm.
 * User: Rene
 * Date: 23.07.2018
 * Time: 20:11
 */
?>
<div class="col">
    <div id="accordion">
        <div class="card">
            <div class="card-header" id="headingOne">
                <div class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <?php echo get_string('choose_coauthors', 'assignsubmission_author');?>
                </div>
            </div>

            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                <div class="container">
                    <div class="card-body">
                        <div class="too-many-alert hidden">
                            <div class="alert alert-danger">
                                Too many co-authors selected!
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col col-4">
                                <div class="form-group align-middle">
                                    <label for="exampleFormControlSelect2">
                                        <?php echo get_string('available_coauthors', 'assignsubmission_author');?>
                                    </label>
                                    <select multiple class="form-control" id="available-co-authors">
                                        <?php
                                            foreach($this->_['choices'] as $key => $value){
                                                echo "<option id=\"co-author-" . $key . "\">" . $value . "</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col col-auto">
                                <p class="justify-content-center align-items-center">
                                    <button type="button" style="margin: 2px 0px;" class="btn btn-primary" id="add-co-author">
                                        >>
                                    </button>
                                    <br>
                                    <button type="button" class="btn btn-primary" id="remove-co-author">
                                        <<
                                    </button>
                                </p>
                            </div>
                            <div class="col col-4">
                                <div class="form-group align-middle">
                                    <label for="exampleFormControlSelect2">
                                        <?php echo get_string('selected_coauthors', 'assignsubmission_author');?>
                                    </label>
                                    <select multiple class="form-control" id="selected-co-authors">
                                        <?php
                                        foreach($this->_['coauthors'] as $key => $value){
                                            echo "<option id=\"co-author-" . $key . "\">" . $value . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                    <label class="form-check-label" for="defaultCheck1">
                                        <?php echo get_string('check:saveasdefault', 'assignsubmission_author');?>
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingTwo">
                <div class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Collapsible Group Item #2
                </div>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                <div class="card-body">
                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingThree">
                <div class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Collapsible Group Item #3
                </div>
            </div>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
                <div class="card-body">
                    Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
                </div>
            </div>
        </div>
    </div>
</div>