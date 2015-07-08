<?php
    require_once 'include/php/connect.php';
    require_once 'ProjectClass.php';
    require_once 'UserClass.php';
    require_once 'AllClass.php';
    require_once 'sessionCheck.php';
    $allObj = new All;
?>
<script>
    $(document).ready(function(e){
        $("#form" ).find('input.tag').tagedit({
          autocompleteURL: 'team_autocomplete.php',
          allowEdit: false
        });
    });
</script>
<div class="row">
    <div class="col s12">
        <div class="new-project-form-header card-panel"><i class="mdi-av-my-library-add"></i> Add a Project</div>
    </div>
    <div class="col s12">
        <div class="card-panel new-project-form-wrapper">
            <form id="form" name="abstractform" action="submitAbstract.php" method="post">
                <?php
                    if(isset($_GET['id']) && !empty($_GET['id'])) {
                       $project = new Project($_GET['id']);
                       $project->getProject($_GET['id']);
                       $title = $project->title;
                       $challengeId = $project->challengeId;
                       $abstract = $project->abstract;
                       $requirement = $project->requirement;
                       $whymak = $project->whymak;
                       $team = $project->getTeamAdmin($_GET['id'], $curUser->id);
                    } else {
                       if(isset($_GET['challengeId']) && !empty($_GET['challengeId'])) {
                            $challengeId = $_GET['challengeId'];
                       } else {
                            $challengeId="";
                       }
                       $title="";
                       $team = "";
                       $abstract="";
                       $requirement="";
                       $whymak="";
                    }
                ?>
                <div class="input-field col s12">
                    <i class="mdi-action-assignment prefix"></i>
                    <input type = "text" name="title" id="form-title" class="validate" value='<?php echo $title;?>'/>
                    <label for = "form-title">App / Project Title</label>
                </div>
                <div class="input-field col s12">
                    <i class="mdi-action-extension prefix"></i>
                    <select name="challenge" id="form-challenge">
                        <option value="0" disabled
                            <?php
                                if(!isset($_GET['id'])) {
                                    echo "selected";
                                }
                            ?>>             Select a Challenge
                        </option>
                        <?php
                            $result = $GLOBALS['db']->raw("SELECT * FROM challenges");
                            while ($row = $result->fetch_assoc()) {
                        ?>
                        <option
                            <?php
                                echo "value='".$row['Id']."'  ";
                                if(isset($_GET['id']) || isset($_GET['challengeId'])) {
                                    if($challengeId == $row['Id'])
                                        echo "selected";
                                }
                            ?>
                        >             <?php echo substr($row['Statement'],0,115)."..." ?>
                        </option>
                        <?php
                            }
                        ?>
                    </select>
                </div>
                <?php
                    if(!isset($_GET['id']) || (isset($_GET['id']) && $team['Status'] == 0)) {
                ?>
                <div class="col s12 admin">
                    <div class="col s12">
                        <label class="form-team"><i class="mdi-social-people"></i>&nbsp;&nbsp;Team Members</label>
                    </div>
                    <div class="input-field col s12">
                        <?php
                            if(isset($_GET['id']) && $team['Status'] == 0) {
                                $result = $GLOBALS['db']->raw("SELECT * FROM user_project WHERE ProjectId='".$_GET['id']."'");
                                $count = $result->num_rows;
                                if($count > 1) {
                                      $i=0;
                                      while ($row = $result->fetch_assoc()) {
                                          $user = new User;
                                          $user->getUser($row['UserId']);
                                          if($curUser->id == $user->id) {
                                              continue;
                                          }
                                          echo '<input type="text" name="tag['.$i.'-a]" value="'.$user->name.' ('.$user->email.')" class="tag"/>';
                                          $i++;
                                      }
                                  } else {
                                      echo "<input type='text' value='' class='tag' name='tag[]'/>";
                                  }
                            } else {
                        ?>
                        <input type="text" value="" class="tag" name="tag[]"/>
                <?php
                        }
                        echo '
                                </div>
                            </div>';
                    } else {
                        $result = $GLOBALS['db']->raw("SELECT * FROM user_project WHERE ProjectId='".$_GET['id']."'");
                        echo '
                            <div class="col s12 non-admin">
                                <div class="col s12">
                                    <label class="form-team"><i class="mdi-social-people"></i>&nbsp;&nbsp;Team Members</label>
                                </div>
                                <ul class="col s12 team">';
                                while ($row = $result->fetch_assoc()) {
                                    $user = new User;
                                    $user->getUser($row['UserId']);
                                    if($row['Status'] == 0) {
                                        echo '
                                            <li>
                                                <div class="team-member">'.$user->name.' (Team Leader)</div>
                                            </li>';
                                    } else {
                                        echo '
                                            <li>
                                                <div class="team-member">'.$user->name.'</div>
                                            </li>';
                                    }
                                }
                        echo '
                                </ul>
                            </div>';
                    }
                ?>
                <div class="input-field col s12">
                    <i class="mdi-action-description prefix"></i>
                    <textarea name="abstract" id="form-abstract" class="validate materialize-textarea"><?php echo str_replace( '<br />', "\n", $abstract )?></textarea>
                    <label for = "form-abstract">Abstract</label>
                </div>
                <div class="input-field col s12">
                    <i class="mdi-action-speaker-notes prefix"></i>
                    <textarea name="requirement" id="form-requirements" class="validate materialize-textarea"><?php echo str_replace( "<br />", "\n",$requirement);?></textarea>
                    <label for = "form-requirements">Requirements</label>
                </div>
                <div class="input-field col s12">
                    <i class="mdi-action-announcement prefix"></i>
                    <textarea name="whymak" id="form-whymakeathon" class="validate materialize-textarea"><?php echo str_replace( '<br />', "\n",$whymak);?></textarea>
                    <label for = "form-whymakeathon">Why Build For A Change?</label>
                </div>
                <div class="input-field col s12">
                    <input type="submit" class="btn-large" value="Submit"/>
                </div>
            </form>
        </div>
    </div>
</div>