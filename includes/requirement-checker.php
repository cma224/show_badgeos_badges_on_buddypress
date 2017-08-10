<?php

class requirementChecker{
    
      function __construct($user_input) {
          $user_in = $user_input;
      }
      
      public function badgeInProgress($startID,$inProgressID,$badgeInProgressName,$completedBadgeName){
      	
		$earnedAndInProgress = badgeos_has_user_earned_achievement($startID,$user_in) && !badgeos_has_user_earned_achievement($inProgressID,$user_in);
		
		if($earnedAndInProgress)
			echo '<span class="'. strtolower(str_replace(" ","-",$completedBadgeName)) .'-rank">'.$badgeInProgressName
			.'<br>[in Progress]<br><br>'.$completedBadgeName.'</span>';
	}
	
	public function badgeCompleted($completedID,$badgeName){
		if(badgeos_has_user_earned_achievement($completedID,$user_in))
			echo '<span class="'.strtolower(str_replace(" ","-",$badgeName)).'-rank"><br>'.$badgeName.'</span>';
	}
    
          
}

?>