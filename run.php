<?php
class Calendar
{

    public $data;
    public $language;
    public function __construct($language)
    {
        $this->language = $language;
        $this->data = json_decode(file_get_contents(__DIR__ . "/data/$language.json"), true);
    }


    public function update($id, $date, $name)
    {
        foreach ($this->data as &$event) {
            if ($event["id"] == $id) {
                $event["name"] = $name;
                $event["date"] = $date;
                return;
            }
        }
        $this->data[] = [
            "id" => $id,
            "name" => $name,
            "date" => $date,
        ];
    }

    public function save()
    {
        file_put_contents(__DIR__ . "/data/$this->language.json", json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}

foreach (["en", "sc", "tc"] as $language) {
    $calendar = new Calendar($language);

    $data = file_get_contents("https://www.1823.gov.hk/common/ical/$language.json");
    //clean the file bom and utf8
    $data = preg_replace('/\x{FEFF}/u', '', $data);
    $new_data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);

    $events = $new_data["vcalendar"][0]["vevent"];

    foreach ($events as $event) {
        $date = substr($event["dtstart"][0], 0, 4) . "-" . substr($event["dtstart"][0], 4, 2) . "-" . substr($event["dtstart"][0], 6, 2);
        $name = $event["summary"];
        $id = $event["uid"];

        $calendar->update($id, $date, $name);
    }

    //save the data
    $calendar->save();
}

file_put_contents(__DIR__ . "/last_update.txt", date("Y-m-d H:i:s"));
