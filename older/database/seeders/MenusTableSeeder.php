<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = array(
            array('id' => '1','name' => 'Header','position' => 'main-menu','data' => '[{"id":"e95lu1cs","text":"Home","icon":null,"href":"\\/","target":"_self","title":null,"children":[]},{"id":"gays8vco","text":"About Us","icon":null,"href":"\\/about-us","target":"_self","title":null,"children":[]},{"id":"cujvodo2","text":"Explore","icon":null,"href":"#","target":"_self","title":null,"children":[{"id":"aoevjyyt","text":"Team","icon":null,"href":"\\/team","target":"_self","title":null,"children":[]},{"id":"bdoss81r","text":"Pricing","icon":null,"href":"\\/pricing","target":"_self","title":null,"children":[]},{"id":"ez6i5okp","text":"Services","icon":null,"href":"\\/services","target":"_self","title":null,"children":[]},{"id":"bpnk03u6","text":"Projects","icon":null,"href":"\\/projects","target":"_self","title":null,"children":[]}]},{"id":"b2hbs7ik","text":"Blogs","icon":null,"href":"\\/blogs","target":"_self","title":null,"children":[]},{"id":"h7hrsutc","text":"Contact Us","icon":null,"href":"\\/contact-us","target":"_self","title":null,"children":[]}]','lang' => 'en','location' => 'web','status' => '1','created_at' => '2023-08-03 16:57:06','updated_at' => '2024-04-29 08:06:41'),
            array('id' => '2','name' => 'Support','position' => 'footer-right','data' => '[{"id":"f4xccfn2","text":"Who we are","icon":null,"href":"\\/about-us","target":"_self","title":null,"children":[]},{"id":"k15ht8xt","text":"My Account","icon":null,"href":"\\/login","target":"_self","title":null,"children":[]},{"id":"2hsi2kij","text":"Our Team","icon":null,"href":"\\/team","target":"_self","title":null,"children":[]},{"id":"j00ekw2e","text":"Pricing","icon":null,"href":"\\/pricing","target":"_self","title":null,"children":[]}]','lang' => 'en','location' => 'web','status' => '1','created_at' => '2023-08-16 17:33:43','updated_at' => '2024-05-18 08:01:54'),
            array('id' => '3','name' => 'Quick Links','position' => 'footer-left','data' => '[{"id":"gaj7mxr8","text":"Home","icon":null,"href":"\\/","target":"_self","title":null,"children":[]},{"id":"8c6fkq6c","text":"About Us","icon":null,"href":"\\/about-us","target":"_self","title":null,"children":[]},{"id":"ekteoag9","text":"Contact Us","icon":null,"href":"\\/contact-us","target":"_self","title":null,"children":[]},{"id":"cbrdfgnc","text":"Blogs","icon":null,"href":"\\/blogs","target":"_self","title":null,"children":[]}]','lang' => 'en','location' => 'web','status' => '1','created_at' => '2023-08-16 17:33:58','updated_at' => '2024-05-02 07:56:10'),
            array('id' => '4','name' => 'Resources','position' => 'footer-center','data' => '[{"id":"5vx4c2ya","text":"Contact Us","icon":null,"href":"\\/contact-us","target":"_self","title":null,"children":[]},{"id":"61ofmtkg","text":"Faqs","icon":null,"href":"\\/faqs","target":"_self","title":null,"children":[]},{"id":"jrjhvbdn","text":"Our Experts","icon":null,"href":"\\/team","target":"_self","title":null,"children":[]},{"id":"7vlla12c","text":"Our Projects","icon":null,"href":"\\/projects","target":"_self","title":null,"children":[]}]','lang' => 'en','location' => 'web','status' => '1','created_at' => '2024-04-29 09:58:17','updated_at' => '2024-05-18 08:01:15')
          );
          

        Menu::insert($menus);
    }
}
