-- CREATE DATABASE caraudio;
USE caraudio;

CREATE TABLE competitions(
    id bigint not null primary key auto_increment,
    title varchar(50) not null,
    subtitle varchar(50) not null,
    banner text default null,
    description text not null,
    type int not null,
    updated_at timestamp default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at timestamp default CURRENT_TIMESTAMP
);

INSERT INTO competitions (id, title, subtitle, banner, description, type) VALUES
(1, 'Audio', 'Sound Quality', 'https://apican.mplaydev.com/public/upload/files/competition/can-audio.jpg', 'The competition that focused on quality of the sound in a car audio system and all related elements including the security element in driving.', 1),
(2, 'Dance', 'Dance competitions the CAN style!', 'https://apican.mplaydev.com/public/upload/files/competition/can-dance.jpg', 'CAN see the performance side in CAN Perform it become more and more happening, fun but serius now, it become another happening industry and we decide to create a new class, we called it CAN Dance.', 2),
(3, 'Photography', 'Finding the best photography', 'https://apican.mplaydev.com/public/upload/files/competition/can-photo.jpg', 'CAN Photography is a competition class that challenges all participants to shoot the best photo.', 3);

CREATE TABLE competition_activities (
    id bigint not null primary key auto_increment,
    name varchar(50) not null,
    competition_id bigint not null,
    winner_count int default 0,
    updated_at timestamp default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at timestamp default CURRENT_TIMESTAMP,
    foreign key (competition_id) references competitions(id)
);

INSERT INTO competition_activities (id, name, competition_id, winner_count) VALUES
(1, 'CAN Q', 1, 3), (2, 'CAN Loud', 1, 3), (3, 'CAN Jam', 1, 3), (4, 'CAN Craft', 1, 3), (5, 'CAN Tune', 1, 3), (6, 'CAN Perform', 1, 3), (7, 'CAN Dance', 2, 3), (8, 'CAN Shoot', 3, 3);

CREATE TABLE class_grades(
    id bigint not null primary key auto_increment,
    name varchar(50) not null,
    alias varchar(50) not null,
    updated_at timestamp default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at timestamp default CURRENT_TIMESTAMP
);

INSERT INTO class_grades (id, name, alias) VALUES (1, 'Consumer', 'C Class'), (2, 'Prosumer', 'S Class'), (3, 'Professional', 'P'), (4, 'Pro Extreme', '');

CREATE TABLE class_countries(
    id bigint not null primary key auto_increment,
    name varchar(50) not null,
    updated_at timestamp default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at timestamp default CURRENT_TIMESTAMP
);

INSERT INTO class_countries(id, name) VALUES (1, 'INTERNATIONAL'), (2, 'INDONESIA');

CREATE TABLE class_categories(
    id bigint not null primary key auto_increment,
    name varchar(50) not null,
    competition_activity_id bigint not null,
    updated_at timestamp default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at timestamp default CURRENT_TIMESTAMP,
    foreign key (competition_activity_id) references competition_activities(id)
);

INSERT INTO class_categories(id, name, competition_activity_id) VALUES
(1, '2 Way Classes', 1), (2, '3 Way and above Classes', 1), (3, 'Extreme', 1), (4, 'Special', 1), (5, 'Budget 1', 2), (6, 'Budget 2', 2), (7, 'Current 1', 2), (8, 'Current 2', 2), (9, 'Special 1', 2),
(10, 'Special 2', 2), (11, 'Prosumer', 3), (12, 'Professional', 3), (13, 'Budget 1', 6), (14, 'Budget 2', 6), (15, 'Budget 3', 6), (16, 'Special', 6);

CREATE TABLE class_groups(
    id bigint not null primary key auto_increment,
    name varchar(50) not null,
    class_grade_id bigint not null,
    class_country_id bigint default null,
    class_category_id bigint default null,
    updated_at timestamp default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at timestamp default CURRENT_TIMESTAMP,
    foreign key (class_grade_id) references class_grades(id),
    foreign key (class_country_id) references class_countries(id),
    foreign key (class_category_id) references class_categories(id)
);

INSERT INTO class_groups(id, name, class_grade_id, class_country_id, class_category_id) VALUES
(1, 'C2W1250', 1, 1, 1), (2, 'C2W2000', 1, 1, 1), (3, 'C2W15', 1, 2, 1), (4, 'C2W25', 1, 2, 1), (5, 'C3WU1250', 1, 1, 2), (6, 'C3WU2000', 1, 1, 2), (7, 'C3WU3000', 1, 1, 2),
(8, 'C3WU15', 1, 2, 2), (9, 'C3WU25', 1, 2, 2), (10, 'C3WU50', 1, 2, 2), (11, 'C Extreme', 1, 1, 3), (12, 'C Extreme', 1, 2, 3), (13, 'S2W2250', 2, 1, 1), (14, 'S2W5000', 2, 1, 1),
(15, 'S2W30', 2, 2, 1), (16, 'S2W60', 2, 2, 1), (17, 'S3WU2250', 2, 1, 2), (18, 'S3WU5000', 2, 1, 2), (19, 'S3WU10000', 2, 1, 2), (20, 'S3WU30', 2, 2, 2), (21, 'S3WU60', 2, 2, 2),
(22, 'S3WU120', 2, 2, 2), (23, 'S Extreme', 2, 1, 3), (24, 'S Extreme', 2, 2, 3), (25, 'OEM Look', 2, 1, 4), (26, 'Non-DSP', 2, 1, 4), (27, 'OEM Look', 2, 2, 4), (28, 'Non-DSP', 2, 2, 4),
(29, 'PW10000', 3, 1, 1), (30, 'King of 2 Way', 3, 1, 1), (31, 'P2W120', 3, 2, 1), (32, 'King of 2 Way', 3, 2, 1), (33, 'P3WU10000', 3, 1, 2), (34, 'P3WU20000', 3, 1, 2), (35, 'King of Sound', 3, 1, 2),
(36, 'P3WU120', 3, 2, 2), (37, 'P3WU240', 3, 2, 2), (38, 'King of Sound', 3, 2, 2), (39, 'King of Extreme', 3, 1, 3), (40, 'King of Extreme', 3, 2, 3),
(41, 'C500', 1, 1, 5), (42, 'C5', 1, 2, 5), (43, 'C1250', 1, 1, 6), (44, 'C15', 1, 2, 6), (45, 'C40A', 1, 1, 7), (46, 'C40A', 1, 2, 7), (47, 'C80A', 1, 1, 8), (48, 'C80A', 1, 2, 8),
(49, '3C5', 1, 1, 9), (50, '3C5', 1, 2, 9), (51, '3C15', 1, 1, 10), (52, '3C15', 1, 2, 10), (53, 'S2250', 2, 1, 5), (54, 'S30', 2, 2, 5), (55, 'S5000', 2, 1, 6), (56, 'S60', 2, 2, 6),
(57, 'C120A', 2, 1, 7), (58, 'C120A', 2, 2, 7), (59, 'C240A', 2, 1, 8), (60, 'C240A', 2, 2, 8), (61, 'CAN Kids', 2, 1, 9), (62, 'CAN Kids', 2, 2, 9), (63, 'Unlimited', 3, 1, 5),
(64, 'Unlimited', 3, 2, 5), (65, 'Unlimited', 3, 1, 6), (66, 'Unlimited', 3, 2, 6), (67, 'Unlimited', 3, 1, 7), (68, 'Unlimited', 3, 2, 7), (69, 'Unlimited', 3, 1, 8), (70, 'Unlimited', 3, 2, 8),
(71, 'Headunit', 3, 1, 9), (72, 'Headunit', 3, 2, 9), (73, 'Extreme', 3, 2, 10), (74, 'S 120 dB', 2, 1, 11), (75, 'S 120 dB', 2, 2, 11), (76, 'S 126 dB', 2, 1, 11), (77, 'S 126 dB', 2, 2, 11),
(78, 'S 132 dB', 2, 1, 11), (79, 'S 132 dB', 2, 2, 11), (80, 'S 138 dB', 2, 1, 11), (81, 'S 138 dB', 2, 2, 11), (82, 'P 144 dB', 3, 1, 12), (83, 'P 144 dB', 3, 2, 12), (84, 'P 147 dB', 3, 1, 12),
(85, 'P 147 dB', 3, 2, 12), (86, 'P 150 dB', 3, 1, 12), (87, 'P 150 dB', 3, 2, 12), (88, 'P Extreme unlimited dB', 3, 1, 12), (89, 'P Extreme unlimited dB', 3, 2, 12), (90, 'Consumer', 1, null, null),
(91, 'Prosumer', 2, null, null), (92, 'Professional', 3, null, null), (93, 'Pro Extreme', 4, null, null), (94, 'C2250', 1, 1, 13), (95, 'C30', 1, 2, 13), (96, 'C5000', 1, 1, 14), (97, 'C60', 1, 2, 14),
(98, 'Unlimited', 1, 1, 15), (99, 'Unlimited', 1, 2, 15), (100, 'S5000', 2, 1, 13), (101, 'S60', 2, 2, 13), (102, 'S10000', 2, 1, 14), (103, 'S120', 2, 2, 14), (104, 'Unlimited', 2, 1, 15),
(105, 'Unlimited', 2, 2, 15), (106, 'Unlimited', 3, 1, 15), (107, 'Unlimited', 3, 2, 15), (108, 'Extreme', 3, 1, 16), (109, 'Extreme', 3, 2, 16);


CREATE TABLE countries(
    id bigint not null primary key auto_increment,
    name varchar(50) not null,
    country_code varchar(2) not null,
    updated_at datetime default null,
    created_at datetime default null
);

CREATE TABLE roles(
    id int not null primary key auto_increment,
    role_name varchar(50) not null,
    updated_at timestamp default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at timestamp default CURRENT_TIMESTAMP
);

INSERT INTO roles (role_name) VALUES
('Admin'), ('Contributor'), ('Sponsor'), ('CAN friend'), ('Judge'), ('Participant');

CREATE TABLE users(
    id bigint not null primary key auto_increment,
    name varchar(100) not null,
    email varchar(100) not null,
    email_verified_at datetime default null,
    email_confirm_code varchar(6) default null,
    password text not null,
    api_token varchar(100) default null,
    role_id int not null,
    sponsor_type int default NULL,
    judge_rating float default null,
    member_rating float default null,
    can_q_consumer_point int default 0,
    can_q_prosumer_point int default 0,
    can_q_professional_point int default 0,
    status_banned int default 0,
    updated_at datetime default null,
    created_at datetime default null
);

CREATE TABLE user_profiles(
    id bigint not null primary key auto_increment,
    avatar text default null,
    banner text default null,
    biography longtext default null,
    phone_no varchar(13) default NULL,
    user_id bigint not null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (user_id) references users(id)
);

CREATE TABLE galleries(
    id bigint not null primary key auto_increment,
    user_id bigint not null,
    image text default null,
    status_delete int default 0,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (user_id) references users(id)
);

CREATE TABLE country_sponsors(
    id bigint not null primary key auto_increment,
    user_id bigint not null,
    country_id bigint not null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (country_id) references countries(id),
    foreign key (user_id) references users(id)
);

CREATE TABLE event_types(
    id bigint not null primary key auto_increment,
    name varchar(100) not null,
    factor int default 0,
    updated_at timestamp default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at timestamp default CURRENT_TIMESTAMP
);

INSERT INTO event_types (id, name, factor) VALUES
(1, 'National', 3), (2, 'CAN Partnership', 2), (3, 'Campus, Schools, Communities on small scale, Sponsor Event', 1);

CREATE TABLE events(
    id bigint not null primary key auto_increment,
    banner text default null,
    title varchar(100) not null,
    description text not null,
    recap longtext default null,
    date_start varchar(10) not null,
    date_end varchar(10) not null,
    time_start varchar(5) not null,
    time_end varchar(5) not null,
    date_time_start datetime not null,
    date_time_end datetime not null,
    location varchar(100) not null,
    contact_name varchar(50) default null,
    contact_phone varchar(20) default null,
    status_can_final int default 0,
    status_score_final int default 0,
    event_type_id bigint not null,
    event_country_id bigint not null,
    competition_activity text not null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_type_id) references event_types(id),
    foreign key (event_country_id) references class_countries(id)
);

CREATE TABLE cars(
    id bigint not null primary key auto_increment,
    user_id bigint not null,
    avatar text default null,
    engine int not null,
    power int not null,
    seat int not null,
    transmission_type enum("Automatic", "Manual") not null,
    vehicle varchar(50) not null,
    license_plate varchar(20) not null,
    vin_number varchar(20) not null,
    type varchar(20) not null,
    color varchar(25) not null,
    front_car_image text default NULL,
    headunits text default null,
    processor text default null,
    power_amplifier text default null,
    speakers text default null,
    wires text default null,
    signal_flowchart text default null,
    power_supply_flowchart text default null,
    other_devices text default null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (user_id) references users(id)
);

CREATE TABLE event_sponsors(
    id bigint not null primary key auto_increment,
    event_id bigint not null,
    sponsor_id bigint not null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_id) references events(id),
    foreign key (sponsor_id) references users(id)
);

CREATE TABLE dance_major_aspects (
    id bigint not null primary key auto_increment,
    name varchar(50) not null,
    updated_at timestamp default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at timestamp default CURRENT_TIMESTAMP
);

INSERT INTO dance_major_aspects(id, name) VALUES
(1, 'Creativity'), (2, 'The Ability of Motion'), (3, 'Body Language'), (4, 'Show Impression');

CREATE TABLE dance_sub_assessments(
    id bigint not null primary key auto_increment,
    name varchar(50) not null,
    dance_major_aspect_id bigint not null,
    updated_at timestamp default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at timestamp default CURRENT_TIMESTAMP,
    foreign key (dance_major_aspect_id) references dance_major_aspects(id)
);

INSERT INTO dance_sub_assessments(id, name, dance_major_aspect_id) VALUES
(1, 'Theme', 1), (2, 'Choreography', 1), (3, 'Costumes and Property', 1), (4, 'Quality of Motion', 2), (5, 'Balance', 2), (6, 'Dynamics and Speed', 2), (7, 'Lower Body Parts', 3),
(8, 'Upper Body Parts', 3), (9, 'Facial Expression', 3), (10, 'Team Confidence', 4), (11, 'Influence to the Audience', 4);

CREATE TABLE event_members(
    id bigint not null primary key auto_increment,
    event_id bigint not null,
    member_id bigint not null,
    competition_label text default NULL,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_id) references events(id),
    foreign key (member_id) references users(id)
);

CREATE TABLE event_judges(
    id bigint not null primary key auto_increment,
    event_id bigint not null,
    judge_id bigint not null,
    competition_label text default NULL,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_id) references events(id),
    foreign key (judge_id) references users(id)
);

CREATE TABLE event_member_classes (
    id bigint not null primary key auto_increment,
    total_participants int default 0,
    assessed text default null,
    event_member_id bigint not null,
    competition_activity_id bigint not null,
    class_group_id bigint not null,
    class_grade_id bigint not null,
    car_id bigint default null,
    studio_info text default NULL,
    gear text default NULL,
    team_name text default NULL,
    victory_point int default 0,
    disqualified_status int default 0,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_id) references event_members(id),
    foreign key (competition_activity_id) references competition_activities(id),
    foreign key (class_group_id) references class_groups(id),
    foreign key (class_grade_id) references class_grades(id),
    foreign key (car_id) references cars(id)
);

CREATE TABLE event_judge_activities(
    id bigint not null primary key auto_increment,
    event_judge_id bigint not null,
    competition_activity_id bigint not null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_judge_id) references event_judges(id),
    foreign key (competition_activity_id) references competition_activities(id)
);

CREATE TABLE event_judge_member_assignments (
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    event_judge_activity_id bigint not null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id),
    foreign key (event_judge_activity_id) references event_judge_activities(id)
);

CREATE TABLE event_member_results(
    id bigint not null primary key auto_increment,
    provocative int default NULL,
    fairness int default NULL,
    cooperation int default NULL,
    rules_competency int default NULL,
    scale int not null,
    score int not null,
    event_judge_member_assignment_id bigint not null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_judge_member_assignment_id) references event_judge_member_assignments(id)
);

CREATE TABLE event_judge_results(
    id bigint not null primary key auto_increment,
    attitude int not null,
    judging int not null,
    scoring int not null,
    cooperation_and_explanation int not null,
    scale int not null,
    score int not null,
    event_judge_member_assignment_id bigint not null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_judge_member_assignment_id) references event_judge_member_assignments(id)
);

CREATE TABLE news(
    id bigint not null primary key auto_increment,
    title text not null,
    subtitle text not null,
    content longtext not null,
    thumbnail text not null,
    user_id bigint not null,
    country_id bigint not null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (user_id) references users(id),
    foreign key (country_id) references countries(id)
);

CREATE TABLE links(
    id bigint not null primary key auto_increment,
    link text default null,
    expired_time datetime default null,
    updated_at datetime default null,
    created_at datetime default null
);



-- ASSESSMENT

CREATE TABLE can_q_scores (
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    vision_block float default 0,
    seating_position float default 0,
    noise_floor float default 0,
    alternator_whine float default 0,
    coming_late float default 0,
    system_down float default 0,
    system_volume_level_suggested_one float default 0,
    system_volume_level_suggested_two float default 0,
    system_volume_level_suggested_three float default 0,
    system_volume_level_suggested_use float default 0,
    cheating_action float default 0,
    cheating_comment text default null,
    deduction_point float default 0,
    deduction_comment text default NULL,
    grand_total float default 0,
    time_start datetime default null,
    time_end datetime default null,
    status_assessment int default 0,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id)
);

CREATE TABLE can_q_imaging_position_and_focuses(
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    left_drum float default 0,
    left_guitar float default 0,
    left_piano float default 0,
    left_vibraphone float default 0,
    left_trumpet float default 0,
    left_total float default 0,
    lfctr_drum float default 0,
    lfctr_guitar float default 0,
    lfctr_piano float default 0,
    lfctr_vibraphone float default 0,
    lfctr_trumpet float default 0,
    lfctr_total float default 0,
    center_drum float default 0,
    center_guitar float default 0,
    center_piano float default 0,
    center_vibraphone float default 0,
    center_trumpet float default 0,
    center_total float default 0,
    rhctr_drum float default 0,
    rhctr_guitar float default 0,
    rhctr_piano float default 0,
    rhctr_vibraphone float default 0,
    rhctr_trumpet float default 0,
    rhctr_total float default 0,
    right_drum float default 0,
    right_guitar float default 0,
    right_piano float default 0,
    right_vibraphone float default 0,
    right_trumpet float default 0,
    right_total float default 0,
    total_imaging_position_and_focus float default 0,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id)
);

CREATE TABLE can_q_stagings (
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    staging_left float default 0,
    staging_right float default 0,
    height_left float default 0,
    height_lfctr float default 0,
    height_center float default 0,
    height_rhctr float default 0,
    height_right float default 0,
    height_total float default 0,
    distance_left float default 0,
    distance_lfctr float default 0,
    distance_center float default 0,
    distance_rhctr float default 0,
    distance_right float default 0,
    distance_total float default 0,
    depth_c1_to_c2 float default 0,
    depth_c2_to_c3 float default 0,
    depth_total float default 0,
    staging_total float default 0,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id)
);

CREATE TABLE can_q_listening_pleasures(
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    listening_low_distorted float default 0,
    listening_low_vibration float default 0,
    listening_low_loudness float default 0,
    listening_low_rear_bass float default 0,
    listening_low_less_low_extention float default 0,
    listening_low_boomy_blur_muddy float default 0,
    listening_low_definition float default 0,
    listening_low_total float default 0,
    listening_mid_bass_distorted float default 0,
    listening_mid_bass_vibration float default 0,
    listening_mid_bass_loudness float default 0,
    listening_mid_bass_position_unstable float default 0,
    listening_mid_bass_lr_timbre_different float default 0,
    listening_mid_bass_stiff_thin_dry float default 0,
    listening_mid_bass_boomy_blur_muddy float default 0,
    listening_mid_bass_definition float default 0,
    listening_mid_bass_total float default 0,
    listening_mid_low_distorted float default 0,
    listening_mid_low_loudness float default 0,
    listening_mid_low_position_unstable float default 0,
    listening_mid_low_lr_timbre_different float default 0,
    listening_mid_low_clinical_thin_dry float default 0,
    listening_mid_low_boxy_blur_muddy float default 0,
    listening_mid_low_definition float default 0,
    listening_mid_low_total float default 0,
    listening_mid_high_distorted float default 0,
    listening_mid_high_loudness float default 0,
    listening_mid_high_position_unstable float default 0,
    listening_mid_high_lr_timbre_different float default 0,
    listening_mid_high_clinical_dry float default 0,
    listening_mid_high_blur_honkey float default 0,
    listening_mid_high_harsh_sibilance float default 0,
    listening_mid_high_total float default 0,
    listening_high_distorted float default 0,
    listening_high_loudness float default 0,
    listening_high_lr_timbre_different float default 0,
    listening_high_dry_clinical_metallic float default 0,
    listening_high_blur_dull float default 0,
    listening_high_harsh_sibilance float default 0,
    listening_high_total float default 0,
    listening_total float default 0,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id)
);

CREATE TABLE can_q_tonal_accuracies(
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    tonal_low float default 0,
    tonal_mid_bass float default 0,
    tonal_mid_low float default 0,
    tonal_mid_high float default 0,
    tonal_high float default 0,
    tonal_total float default 0,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id)
);

CREATE TABLE can_q_spectral_balance_and_linearities(
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    spectral_balance float default 0,
    linearity float default 0,
    spectral_balance_and_linearity_total float default 0,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id)
);

CREATE TABLE can_loud_scores(
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    first_round float default 0,
    second_round float default 0,
    deduction_battery float default 0,
    deduction_point float default 0,
    deduction_comment text default NULL,
    total float default 0,
    status_assessment int default 0,
    time_start datetime default null,
    time_end datetime default null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id)
);

CREATE TABLE can_jam_score_histories(
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    db_score float default 0,
    system_down float default 0,
    deduction_point float default 0,
    deduction_comment text default NULL,
    total float default 0,
    time_start datetime default null,
    time_end datetime default null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id)
);

CREATE TABLE can_jam_scores(
    id bigint not null primary key auto_increment,
    can_jam_score_history_id bigint not null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (can_jam_score_history_id) references can_jam_score_histories(id)
);

CREATE TABLE can_craft_scores(
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    connection_quality float default 0,
    main_fuse_value float default 0,
    wire_length float default 0,
    fuse_value float default 0,
    product_mounting float default 0,
    overall_wiring float default 0,
    overall_workmanship_safety_factor float default 0,
    protection_quality float default 0,
    main_fuse_connection_quality float default 0,
    wire_penetration float default 0,
    mounting_quality float default 0,
    fuse_block float default 0,
    all_main_equipment_connection_quality float default 0,
    overall_workmanship_quality float default 0,
    battery_housing float default 0,
    mounting_quality_of_front_fuse float default 0,
    additional_ground_wire float default 0,
    detail_workmanship float default 0,
    overall_design_and_ideas float default 0,
    deduction_point float default 0,
    deduction_comment text default NULL,
    total float default 0,
    time_start datetime default null,
    time_end datetime default null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id)
);

CREATE TABLE can_craft_pro_extremes(
    id bigint not null primary key auto_increment,
    items text default null,
    comment_participant text default null,
    comment_judge text default null,
    point float default 0,
    event_member_class_id bigint not null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id)
);

CREATE TABLE can_tune_brackets(
    id bigint not null primary key auto_increment,
    name varchar(20) not null,
    class_grade_id bigint not null,
    updated_at timestamp default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at timestamp default CURRENT_TIMESTAMP,
    foreign key (class_grade_id) references class_grades (id)
);

INSERT INTO can_tune_brackets (id, name, class_grade_id) VALUES (1, 'A', 1), (2, 'B', 1), (3, 'C', 1), (4, 'D', 1), (5, 'E', 1),
(6, 'F', 1), (7, 'ABC', 2), (8, 'DEF', 2), (9, 'Professional', 3);

CREATE TABLE can_tune_consumer_pyramids(
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    tonal_low float default 0,
    tonal_mid_bass float default 0,
    tonal_mid_low float default 0,
    tonal_mid_high float default 0,
    tonal_high float default 0,
    tonal_total float default 0,
    deduction_point float default 0,
    deduction_comment text default NULL,
    total float default 0,
    can_tune_bracket_id bigint not null,
    time_start datetime default null,
    time_end datetime default null,
    status_assessment int default 0,
    status_submit_prosumer int default 0,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id),
    foreign key (can_tune_bracket_id) references can_tune_brackets(id)
);

CREATE TABLE can_tune_prosumer_pyramids(
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    tonal_low float default 0,
    tonal_mid_bass float default 0,
    tonal_mid_low float default 0,
    tonal_mid_high float default 0,
    tonal_high float default 0,
    tonal_total float default 0,
    deduction_point float default 0,
    deduction_comment text default NULL,
    total float default 0,
    can_tune_bracket_id bigint not null,
    time_start datetime default null,
    time_end datetime default null,
    status_assessment int default 0,
    status_submit_professional int default 0,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id),
    foreign key (can_tune_bracket_id) references can_tune_brackets(id)
);

CREATE TABLE can_tune_professional_pyramids(
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    tonal_low float default 0,
    tonal_mid_bass float default 0,
    tonal_mid_low float default 0,
    tonal_mid_high float default 0,
    tonal_high float default 0,
    tonal_total float default 0,
    staging_left float default 0,
    staging_right float default 0,
    height_left float default 0,
    height_lfctr float default 0,
    height_center float default 0,
    height_rhctr float default 0,
    height_right float default 0,
    height_total float default 0,
    distance_left float default 0,
    distance_lfctr float default 0,
    distance_center float default 0,
    distance_rhctr float default 0,
    distance_right float default 0,
    distance_total float default 0,
    depth_c1_to_c2 float default 0,
    depth_c2_to_c3 float default 0,
    depth_total float default 0,
    staging_total float default 0,
    deduction_point float default 0,
    deduction_comment text default NULL,
    grand_total float default 0,
    time_start datetime default null,
    time_end datetime default null,
    status_assessment int default 0,
    status_submit_final int default 0,
    can_tune_bracket_id bigint not null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id),
    foreign key (can_tune_bracket_id) references can_tune_brackets(id)
);

CREATE TABLE can_perform_scores(
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    tonal_low float default 0,
    tonal_mid_bass float default 0,
    tonal_mid_low float default 0,
    tonal_mid_high float default 0,
    tonal_high float default 0,
    spectral_balance float default 0,
    linearity float default 0,
    noise_floor float default 0,
    alternator_whine float default 0,
    coming_late float default 0,
    system_down float default 0,
    tonal_total float default 0,
    theme float default 0,
    choreography float default 0,
    costume float default 0,
    movement_quality float default 0,
    balance float default 0,
    dynamic_and_speed float default 0,
    lower_body_activity float default 0,
    upper_body_activity float default 0,
    facial_expression float default 0,
    team_confidence float default 0,
    impact_to_audience float default 0,
    dance_total float default 0,
    deduction_point float default 0,
    deduction_comment text default NULL,
    grand_total float default 0,
    time_start datetime default null,
    time_end datetime default null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id)
);

CREATE TABLE can_dance_scores(
    id bigint not null primary key auto_increment,
    event_member_class_id bigint not null,
    theme float default 0,
    choreography float default 0,
    costume float default 0,
    movement_quality float default 0,
    balance float default 0,
    dynamic_and_speed float default 0,
    lower_body_activity float default 0,
    upper_body_activity float default 0,
    facial_expression float default 0,
    team_confidence float default 0,
    impact_to_audience float default 0,
    dance_total float default 0,
    deduction_point float default 0,
    deduction_comment text default NULL,
    grand_total float default 0,
    time_start datetime default null,
    time_end datetime default null,
    updated_at datetime default null,
    created_at datetime default null,
    foreign key (event_member_class_id) references event_member_classes(id)
);

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Table structure for table `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for table `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Table structure for table `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for table `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`);

--
-- Table structure for table `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(3, NULL, 'Laravel Personal Access Client', '4OLl1Z9g9mBbtwsOsOLfuQaYCscGXuD8Iav5sCWY', 'http://localhost', 1, 0, 0, '2019-05-13 02:04:39', '2019-05-13 02:04:39'),
(4, NULL, 'Laravel Password Grant Client', 'NwIKwxidWaW791BeNVYgdl47Ye5aWvZgphfyNU3d', 'http://localhost', 0, 1, 0, '2019-05-13 02:04:39', '2019-05-13 02:04:39');

--
-- Indexes for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- AUTO_INCREMENT for table `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Table structure for table `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` int(10) UNSIGNED NOT NULL,
  `client_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(2, 3, '2019-05-13 02:04:39', '2019-05-13 02:04:39');

--
-- Indexes for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_personal_access_clients_client_id_index` (`client_id`);

--
-- AUTO_INCREMENT for table `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Table structure for table `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for table `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

-- Execution for can.mplaydev.com
-- ALTER TABLE `cars` ADD `vin_number` VARCHAR(20) NOT NULL AFTER `license_plate`, ADD `type` VARCHAR(20) NOT NULL AFTER `vin_number`;
