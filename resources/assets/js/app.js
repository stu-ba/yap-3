/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap')


$(function () {
    $('a.external').click(function (e) {
        e.preventDefault(); // Prevent the href from redirecting directly
        var linkURL = $(this).attr("href");
        warnBeforeRedirect(linkURL);
    });

    $('a.invite-user').click(function (e) {
        e.preventDefault()
        inviteUser()
    })

    $('a.ban-user').click(function (e) {
        e.preventDefault()
        var username = $(this).attr("data-username");
        banUser(username)
    })

    $('a.unban-user').click(function (e) {
        e.preventDefault()
        var username = $(this).attr("data-username");
        unbanUser(username)
    })

    $('a.promote-user').click(function (e) {
        e.preventDefault()
        var username = $(this).attr("data-username");
        promoteUser(username)
    })

    $('a.demote-user').click(function (e) {
        e.preventDefault()
        var username = $(this).attr("data-username");
        demoteUser(username)
    })
});

function warnBeforeRedirect(linkURL) {
    swal({
        title: "Leave this site?",
        text: "If you click 'OK', you will be redirected to " + linkURL,
        type: "warning",
        timer: 5000,
        showCancelButton: true
    }).then(
        function () {
            window.location = linkURL;
        },
        function (dismiss) {
        }
    )
}

function inviteUser() {
    swal({
        title: 'Invite user via email.',
        input: 'email',
        inputAttributes: {
            autocomplete: 'off',
            autocorrect: 'off',
            autocapitalize: 'off',
            spellcheck: false
        },
        showCancelButton: true,
        confirmButtonText: 'Invite',
        cancelButtonText: 'More options',
        showLoaderOnConfirm: true,
        preConfirm: function (email) {
            return new Promise(function (resolve, reject) {
                axios.post('/api/invitations', {'email': email}).then(function (response) {
                    resolve(response)
                }).catch(function (error) {
                    if (error.response.data.email != null && typeof error.response.data.email[0] != 'undefined')
                        reject(error.response.data.email[0])
                    reject(error)
                });
            })
        },
        allowOutsideClick: false,
        showCloseButton: true
    }).then(function () {
        swal({
            type: 'success',
            title: 'Invitation sent!',
            timer: 1500
        }).then(
            function () {
            },
            function (dismiss) {
            }
        )
    }).catch(function (reason) {
        if (reason == 'cancel') {
            new route('invitations.create', {'email': swal.getInput().value}).run()
        }
    })
}

function banUser(username) {
    swal({
        title: 'Ban user \''+ username +'\'.',
        input: 'text',
        type: 'error',
        inputValue: 'User terribly misbehaved! End of story.',
        inputAttributes: {
            autocomplete: 'off',
            autocorrect: 'off',
            autocapitalize: 'off',
            spellcheck: true,
        },
        showCancelButton: true,
        confirmButtonText: 'Ban',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: function (text) {
            return new Promise(function (resolve, reject) {
                axios.post('/api/users/' + username + '/ban', {'reason': text}).then(function (response) {
                    resolve(response)
                }).catch(function (error) {
                    if (error.response.data.reason != null && typeof error.response.data.reason[0] != 'undefined')
                        reject(error.response.data.reason[0])
                    reject(error)
                });
            })
        },
        allowOutsideClick: false,
        showCloseButton: true
    }).then(function () {
        swal({
            type: 'success',
            title: 'User banned!',
            timer: 2000
        }).then(
            function () {
                window.location.reload(false);
            },
            function (dismiss) {
                window.location.reload(false);
            }
        )
    }).catch(function (reason) {})
}

function unbanUser(username) {
    swal({
        title: 'Unban user \''+ username +'\'?',
        type: 'question',
        showCancelButton: true,
        confirmButtonText: 'Remove ban',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise(function (resolve, reject) {
                axios.post('/api/users/' + username + '/unban').then(function (response) {
                    resolve(response)
                }).catch(function (error) {
                    reject(error)
                });
            })
        },
        allowOutsideClick: false,
        showCloseButton: true
    }).then(function () {
        swal({
            type: 'success',
            title: 'User unbanned!',
            timer: 2000
        }).then(
            function () {
                window.location.reload(false);
            },
            function (dismiss) {
                window.location.reload(false);
            }
        )
    }).catch(function (reason) {})
}

function promoteUser(username) {
    swal({
        title: 'Promote user \''+ username +'\'?',
        type: 'question',
        showCancelButton: true,
        confirmButtonText: 'Promote',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise(function (resolve, reject) {
                axios.post('/api/users/' + username + '/promote').then(function (response) {
                    resolve(response)
                }).catch(function (error) {
                    reject(error)
                });
            })
        },
        allowOutsideClick: false,
        showCloseButton: true
    }).then(function () {
        swal({
            type: 'success',
            title: 'User promoted!',
            timer: 2000
        }).then(
            function () {
                window.location.reload(false);
            },
            function (dismiss) {
                window.location.reload(false);
            }
        )
    }).catch(function (reason) {})
}

function demoteUser(username) {
    swal({
        title: 'Demote user \''+ username +'\'?',
        type: 'question',
        showCancelButton: true,
        confirmButtonText: 'Demote',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise(function (resolve, reject) {
                axios.post('/api/users/' + username + '/demote').then(function (response) {
                    resolve(response)
                }).catch(function (error) {
                    reject(error)
                });
            })
        },
        allowOutsideClick: false,
        showCloseButton: true
    }).then(function () {
        swal({
            type: 'success',
            title: 'User demoted!',
            timer: 2000
        }).then(
            function () {
                window.location.reload(false);
            },
            function (dismiss) {
                window.location.reload(false);
            }
        )
    }).catch(function (reason) {})
}

function route(route, parameters = {}) {
    this.fetch = function () {
        return new Promise(function (resolve, reject) {
            axios('api/router/' + route + '/' + encodeURIComponent(JSON.stringify(parameters))).then(function (response) {
                resolve(response.data.url)
            }).catch(function (error) {
                reject(error)
            })
        })
    }

    var fetched = this.fetch()

    this.redirect = function () {
        fetched.then(function (url) {
            window.location = url;
        }).catch(function (error) {
        });
    }

    this.run = function () {
        return this.redirect()
    }
}
