// Main navigation entries; labels are translation keys.
export const mainNav = [
    { label: 'nav.home', route: 'home' },
    { label: 'nav.exams', route: 'exams.index', active: 'exams.*' },
    { label: 'nav.questions', route: 'questions' },
    { label: 'nav.rooms', route: 'rooms' },
    { label: 'nav.clubs', route: 'clubs' },
];

export const adminNav = [
    { label: 'nav.admin_users', route: 'admin.users.index' },
    { label: 'nav.admin_import', route: 'admin.students.import' },
];
