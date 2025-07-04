<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {



        /* `ecard`.`categories` */

        /* `ecard`.`categories` */
        $categories = array(
            array('id' => '65', 'title' => 'Developement', 'slug' => 'developement-1', 'icon' => NULL, 'preview' => '/demo/jHCdW5MbcEb1uc4oIejC.png', 'type' => 'project', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-21 08:26:48', 'updated_at' => '2024-04-01 21:15:47'),
            array('id' => '66', 'title' => 'CRM Management', 'slug' => 'crm-management', 'icon' => '/demo/fuDjg2gGevqWxgzfDpSF.svg', 'preview' => '/uploads/23/11/IohamiwnhiEelxUP21oS.png', 'type' => 'service', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-21 08:56:31', 'updated_at' => '2024-04-30 07:23:40'),
            array('id' => '67', 'title' => 'Branding', 'slug' => 'branding-1', 'icon' => NULL, 'preview' => '/demo/bPWZ7IpuV1mwbFWWXH9s.png', 'type' => 'project', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-21 16:49:48', 'updated_at' => '2024-04-01 21:15:22'),
            array('id' => '68', 'title' => 'Design Work', 'slug' => 'design-work', 'icon' => NULL, 'preview' => '/demo/unwMQs7oCzBe2Mp5sXcf.png', 'type' => 'project', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-21 16:54:13', 'updated_at' => '2024-04-01 21:14:48'),
            array('id' => '70', 'title' => 'Project Management', 'slug' => 'project-management', 'icon' => '/demo/4ViiM3dGeuSHL6OBFB6Q.svg', 'preview' => '/uploads/23/11/IohamiwnhiEelxUP21oS.png', 'type' => 'service', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-23 09:34:21', 'updated_at' => '2024-04-30 07:23:31'),
            array('id' => '71', 'title' => 'Sales Analytics', 'slug' => 'sales-analytics', 'icon' => '/demo/ToCWDe8KxDFZ3syFYWgp.svg', 'preview' => '/uploads/23/11/IohamiwnhiEelxUP21oS.png', 'type' => 'service', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-23 09:36:37', 'updated_at' => '2024-04-30 07:23:17'),
            array('id' => '72', 'title' => 'Easy Envoicing', 'slug' => 'easy-envoicing', 'icon' => '/demo/J3rkyBa4RhMGTfcVXB06.svg', 'preview' => '/uploads/23/11/IohamiwnhiEelxUP21oS.png', 'type' => 'service', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-23 09:36:44', 'updated_at' => '2024-04-30 07:23:04'),
            array('id' => '73', 'title' => 'Complete Visibility', 'slug' => 'complete-visibility', 'icon' => '/demo/FLoHd3AajmtZpHJYaryX.svg', 'preview' => '/uploads/23/11/IohamiwnhiEelxUP21oS.png', 'type' => 'service', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-23 09:36:54', 'updated_at' => '2024-04-30 07:22:17'),
            array('id' => '74', 'title' => 'Illusutration', 'slug' => 'illusutration-1', 'icon' => NULL, 'preview' => '/demo/qhFsGz2ZL3d89Sf36xZM.png', 'type' => 'project', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-21 16:54:13', 'updated_at' => '2024-04-01 21:14:53'),
            array('id' => '75', 'title' => 'App Design', 'slug' => 'app-design-1', 'icon' => NULL, 'preview' => '/demo/uAjcXlqIUHkplqP8sTq1.png', 'type' => 'project', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-21 16:54:13', 'updated_at' => '2024-04-01 21:14:59'),
            array('id' => '76', 'title' => 'Technology', 'slug' => 'technology', 'icon' => NULL, 'preview' => NULL, 'type' => 'blog_category', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-23 10:45:29', 'updated_at' => '2024-04-30 09:11:46'),
            array('id' => '77', 'title' => 'Tools', 'slug' => 'tools', 'icon' => NULL, 'preview' => NULL, 'type' => 'blog_category', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-23 10:45:40', 'updated_at' => '2024-04-30 09:11:36'),
            array('id' => '78', 'title' => 'Sales', 'slug' => 'sales', 'icon' => NULL, 'preview' => NULL, 'type' => 'blog_category', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-23 10:45:50', 'updated_at' => '2024-04-30 09:11:28'),
            array('id' => '79', 'title' => 'sports', 'slug' => 'sports', 'icon' => NULL, 'preview' => NULL, 'type' => 'tags', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-23 10:46:14', 'updated_at' => '2024-04-30 09:12:35'),
            array('id' => '80', 'title' => 'tech', 'slug' => 'tech', 'icon' => NULL, 'preview' => NULL, 'type' => 'tags', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-23 10:46:25', 'updated_at' => '2024-04-30 09:12:30'),
            array('id' => '81', 'title' => 'ai', 'slug' => 'ai', 'icon' => NULL, 'preview' => NULL, 'type' => 'tags', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2023-11-23 10:46:35', 'updated_at' => '2024-04-30 09:12:40'),
            array('id' => '88', 'title' => '#', 'slug' => '-5', 'icon' => NULL, 'preview' => '/demo/YQMW2XFjvnY0IBvk6T7g.png', 'type' => 'brand', 'status' => '1', 'is_featured' => '1', 'lang' => 'partner', 'category_id' => NULL, 'created_at' => '2024-01-08 03:27:59', 'updated_at' => '2024-03-12 17:17:12'),
            array('id' => '89', 'title' => '#', 'slug' => '-7', 'icon' => NULL, 'preview' => '/demo/tnOvUHqORB6t9umFsujY.png', 'type' => 'brand', 'status' => '1', 'is_featured' => '1', 'lang' => 'partner', 'category_id' => NULL, 'created_at' => '2024-01-08 03:29:30', 'updated_at' => '2024-03-12 17:15:25'),
            array('id' => '90', 'title' => '#', 'slug' => '-8', 'icon' => NULL, 'preview' => '/demo/u81CNif3mk0YXI4Vzqrm.png', 'type' => 'brand', 'status' => '1', 'is_featured' => '1', 'lang' => 'partner', 'category_id' => NULL, 'created_at' => '2024-01-08 03:29:47', 'updated_at' => '2024-03-12 17:14:23'),
            array('id' => '91', 'title' => '#', 'slug' => '-9', 'icon' => NULL, 'preview' => '/demo/44hbQhkbJsbjv09L0Uro.png', 'type' => 'brand', 'status' => '1', 'is_featured' => '1', 'lang' => 'partner', 'category_id' => NULL, 'created_at' => '2024-01-08 03:30:11', 'updated_at' => '2024-03-12 17:08:51'),
            array('id' => '92', 'title' => 'Design', 'slug' => 'design', 'icon' => NULL, 'preview' => '/demo/oMQ9roAM5jBckMKoml1o.png', 'type' => 'brand', 'status' => '1', 'is_featured' => '0', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2024-01-05 06:25:41', 'updated_at' => '2024-01-08 03:32:49'),
            array('id' => '93', 'title' => 'Development ', 'slug' => 'development', 'icon' => NULL, 'preview' => '/demo/oMQ9roAM5jBckMKoml1o.png', 'type' => 'brand', 'status' => '1', 'is_featured' => '0', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2024-01-05 06:25:41', 'updated_at' => '2024-01-08 03:32:49'),
            array('id' => '94', 'title' => 'Art', 'slug' => 'art', 'icon' => NULL, 'preview' => '/demo/oMQ9roAM5jBckMKoml1o.png', 'type' => 'brand', 'status' => '1', 'is_featured' => '0', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2024-01-05 06:25:41', 'updated_at' => '2024-01-08 03:32:49'),
            array('id' => '95', 'title' => 'For all purchases', 'slug' => 'for-all-purchases', 'icon' => NULL, 'preview' => NULL, 'type' => 'card_category', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2024-02-29 17:49:13', 'updated_at' => '2024-02-29 17:49:13'),
            array('id' => '96', 'title' => 'For advertisment', 'slug' => 'for-advertisment', 'icon' => NULL, 'preview' => NULL, 'type' => 'card_category', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2024-02-29 17:49:25', 'updated_at' => '2024-02-29 17:49:25'),
            array('id' => '97', 'title' => 'For Facebook adv', 'slug' => 'for-facebook-adv', 'icon' => NULL, 'preview' => NULL, 'type' => 'card_category', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2024-02-29 17:49:38', 'updated_at' => '2024-02-29 17:49:38'),
            array('id' => '98', 'title' => 'For advertisement in Google', 'slug' => 'for-advertisement-in-google', 'icon' => NULL, 'preview' => NULL, 'type' => 'card_category', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2024-02-29 17:49:50', 'updated_at' => '2024-02-29 17:49:50'),
            array('id' => '99', 'title' => 'For advertisement in TikTok', 'slug' => 'for-advertisement-in-tiktok', 'icon' => NULL, 'preview' => NULL, 'type' => 'card_category', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2024-02-29 17:50:00', 'updated_at' => '2024-02-29 17:50:00'),
            array('id' => '104', 'title' => '#', 'slug' => '-4', 'icon' => NULL, 'preview' => '/demo/120x43.png', 'type' => 'partner', 'status' => '1', 'is_featured' => '1', 'lang' => 'partner', 'category_id' => NULL, 'created_at' => '2024-04-01 10:43:02', 'updated_at' => '2024-04-25 09:56:42'),
            array('id' => '105', 'title' => '#', 'slug' => '-6', 'icon' => NULL, 'preview' => '/demo/120x43.png', 'type' => 'partner', 'status' => '1', 'is_featured' => '1', 'lang' => 'partner', 'category_id' => NULL, 'created_at' => '2024-04-01 10:43:13', 'updated_at' => '2024-04-25 09:56:37'),
            array('id' => '106', 'title' => '#', 'slug' => '-10', 'icon' => NULL, 'preview' => '/demo/120x43.png', 'type' => 'partner', 'status' => '1', 'is_featured' => '1', 'lang' => 'partner', 'category_id' => NULL, 'created_at' => '2024-04-01 10:43:21', 'updated_at' => '2024-04-25 09:56:31'),
            array('id' => '107', 'title' => '#', 'slug' => '-11', 'icon' => NULL, 'preview' => '/demo/120x43.png', 'type' => 'partner', 'status' => '1', 'is_featured' => '1', 'lang' => 'partner', 'category_id' => NULL, 'created_at' => '2024-04-01 10:43:30', 'updated_at' => '2024-04-25 09:56:21'),
            array('id' => '108', 'title' => '#', 'slug' => '-12', 'icon' => NULL, 'preview' => '/demo/120x43.png', 'type' => 'partner', 'status' => '1', 'is_featured' => '1', 'lang' => 'partner', 'category_id' => NULL, 'created_at' => '2024-04-01 10:43:38', 'updated_at' => '2024-04-25 09:55:29'),
            array('id' => '115', 'title' => 'Tech', 'slug' => 'tech', 'icon' => NULL, 'preview' => NULL, 'type' => 'faq_category', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2024-05-02 07:13:55', 'updated_at' => '2024-05-02 07:13:55'),
            array('id' => '116', 'title' => 'Sports', 'slug' => 'sports', 'icon' => NULL, 'preview' => NULL, 'type' => 'faq_category', 'status' => '1', 'is_featured' => '1', 'lang' => 'en', 'category_id' => NULL, 'created_at' => '2024-05-02 07:14:04', 'updated_at' => '2024-05-02 07:14:04')
        );

        Category::insert($categories);
    }
}
