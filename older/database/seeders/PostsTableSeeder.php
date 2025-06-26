<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Post;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $posts = array(
            array('id' => '1','title' => 'What is a virtual prepaid card?','slug' => 'why-does-your-business-need-a-chatbot','type' => 'faq','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 10:17:03','updated_at' => '2023-03-06 10:17:03'),
            array('id' => '2','title' => 'Can I use a virtual prepaid card anywhere?','slug' => 'do-i-need-a-credit-card-to-sign-up','type' => 'faq','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 10:17:50','updated_at' => '2023-03-06 10:17:50'),
            array('id' => '3','title' => 'How does a virtual prepaid card work?','slug' => 'do-you-offer-a-30-day-money-back-guarantee','type' => 'faq','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 10:18:16','updated_at' => '2023-03-06 10:18:16'),
            array('id' => '4','title' => 'Darlene Robertson','slug' => 'Product Manager','type' => 'team','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 17:53:17','updated_at' => '2023-03-06 17:53:17'),
            array('id' => '5','title' => 'Bessie Cooper','slug' => 'Vp People','type' => 'team','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 17:54:06','updated_at' => '2023-03-06 17:54:06'),
            array('id' => '6','title' => 'Eleanor Pena','slug' => 'Head of Design','type' => 'team','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 17:54:44','updated_at' => '2023-03-06 17:54:44'),
            array('id' => '7','title' => 'Rhonda Ortiz','slug' => 'Founder & CO','type' => 'team','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 17:55:23','updated_at' => '2023-03-06 17:55:23'),
            array('id' => '8','title' => 'Why Virtual Prepaid Cards are Perfect for Gifting','slug' => 'why-virtual-prepaid-cards-are-perfect-for-gifting','type' => 'blog','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 18:50:45','updated_at' => '2023-11-23 10:49:22'),
            array('id' => '9','title' => 'How to Securely Manage Your Virtual Prepaid Card','slug' => 'how-to-securely-manage-your-virtual-prepaid-card','type' => 'blog','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 18:57:44','updated_at' => '2023-11-23 10:47:47'),
            array('id' => '10','title' => 'How Virtual Prepaid Cards Enhance Online Security','slug' => 'how-virtual-prepaid-cards-enhance-online-security','type' => 'blog','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 19:00:52','updated_at' => '2023-11-23 10:47:16'),
            array('id' => '12','title' => 'Genghis Khan','slug' => 'Founder & CEO Mongol','type' => 'testimonial','status' => '1','featured' => '1','lang' => '5','created_at' => '2023-03-06 19:23:24','updated_at' => '2024-04-01 10:32:19'),
            array('id' => '13','title' => 'Khan Kong','slug' => 'Founder & CEO Dulalix','type' => 'testimonial','status' => '1','featured' => '1','lang' => '5','created_at' => '2023-03-06 19:24:12','updated_at' => '2024-04-01 10:34:23'),
            array('id' => '14','title' => 'Jane Doe','slug' => 'Founder & CEO Dulalix','type' => 'testimonial','status' => '1','featured' => '1','lang' => '5','created_at' => '2023-03-06 19:24:59','updated_at' => '2024-04-01 10:30:08'),
            array('id' => '15','title' => 'Financial Services','slug' => 'financial-services','type' => 'faq','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 19:30:09','updated_at' => '2023-03-06 19:30:09'),
            array('id' => '16','title' => 'How it will take impact for Food & Restaurants business.','slug' => 'how-it-will-take-impact-for-food-restaurants-business','type' => 'faq','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 19:31:09','updated_at' => '2023-03-06 19:31:09'),
            array('id' => '17','title' => 'Apply job or hire','slug' => 'apply-job-or-hire','type' => 'feature','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 22:24:43','updated_at' => '2023-10-07 09:25:49'),
            array('id' => '18','title' => 'Complete your profile','slug' => 'complete-your-profile','type' => 'feature','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 23:15:36','updated_at' => '2023-10-07 09:24:57'),
            array('id' => '19','title' => 'Create Account','slug' => 'create-account','type' => 'feature','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-03-06 23:32:24','updated_at' => '2023-10-07 09:24:08'),
            array('id' => '26','title' => 'Terms and conditions','slug' => 'terms-and-conditions','type' => 'page','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-04-09 19:40:59','updated_at' => '2023-04-09 19:40:59'),
            array('id' => '27','title' => 'Privacy Policy','slug' => 'privacy-policy','type' => 'page','status' => '1','featured' => '1','lang' => 'en','created_at' => '2023-10-08 05:55:19','updated_at' => '2023-10-08 05:55:19'),
            array('id' => '28','title' => 'Jhone Doe','slug' => 'Founder & CEO Dulalix','type' => 'testimonial','status' => '1','featured' => '1','lang' => '5','created_at' => '2023-03-06 19:24:59','updated_at' => '2024-04-01 10:30:17'),
            array('id' => '30','title' => 'Selma Hardin','slug' => 'Financial Advisor','type' => 'team','status' => '1','featured' => '1','lang' => 'en','created_at' => '2024-04-03 08:52:12','updated_at' => '2024-05-07 17:48:20')
          );
          
        Post::insert($posts);
    }
}
