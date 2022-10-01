<?php 

class aboutMaker
{
    public function makeAbout($data)
    {
        echo $data['name'];
        if(isset($data['exactnetworth']))
        {
            return $this->makeStr("exactnetworth", $data['exactnetworth']['table']);
        }
        
        if(isset($data['thewikifeed']))
        {
            return $this->makeStr("thewikifeed", $data['thewikifeed']['table']);
        }
        
        if(isset($data['starsgab']))
        {
            return $this->makeStr("starsgab", $data['starsgab']['table']);
        }
    }
    
    public function makeStr($crawler, $table)
    {
        print_r($table);
        if($crawler == "starsgab")
        {
            // name
            if(isset($table["Birth Name"]))
            {
                $name = $table["Birth Name"];
            }
        
            else if(isset($table["Real Name"]))
            {
                $name = $table["Real Name"];
            }
        
            else
            {
                die("name not found");
            }
            // dob
            if(isset($table["Birth Date"]))
            {
                $dob = $table["Birth Date"];
            }
        
            else if(isset($table["Birthday"]))
            {
                $dob = $table["Birthday"];
            }
        
            else if(isset($table["Age"]))
            {
                $age = $table["Age"];
            }
        
            else
            {
                die("date not found");
            }
            if(!isset($age))
            {
                $dob = preg_replace('/(\w+) (\d+)\, (\d+)/', "$2 $1 $3", $dob);
        
            }
            
            // birth place
            if(isset($table["Birthplace"]))
            {
                $birthPlace = $table["Birthplace"];
            }
        
            else if(isset($table["Birthplace/ Hometown"]))
            {
                $birthPlace = $table["Birthplace/ Hometown"];
            }
        
            else
            {
                // die("birthPlace not found");
            }
        
            if(isset($table["Profession"]))
            {
                $profession = $table["Profession"];
            }
        
            else if(isset($table["Source of Income"]))
            {
                $profession = $table["Source of Income"];
            }
        
            else if(isset($table["Famous As"]))
            {
                $profession = $table["Famous As"];
            }
        
            // else
            // {
            //     die("profession not found");
            // }
            $worth = $table["Net Worth"];
        }

        if($crawler == "thewikifeed")
        {
            $name = $table["Full Name"];
            $dob = $table["Date of Birth"];
            $dob = preg_replace('/(\d+) (\w+) (\d+)/', "$1 $2 $2", $dob);
            $birthPlace = $table["Birth Place"];
            $profession = $table["Profession"];
            $worth = $table["Net Worth"];
        }
        
        if($crawler == "exactnetworth")
        {
            $name = $table["Full Name"]; 
            $dob = $table["Birth Date"];
            $dob = preg_replace('/(\w+) (\d+)\, (\d+)/', "$2 $1 $3", $dob);
            $birthPlace = $table["Birth Place"];
            $profession = $table["Profession"];
            $worth = $table["Net Worth"];
        }
        

        
        // echo $dob;
        $dob = date_create($dob);
        if($dob !== false && !isset($age))
        {
            $now = date_create(date('d F o'));
            $diff = date_diff($dob, $now);
    
            print_r($crawler . "\t" .  $diff->y . "\n");
        }
    }
}